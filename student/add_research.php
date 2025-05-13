<?php
session_start();
include '../connection/config.php';

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../phpmailer/src/PHPMailer.php';
include '../phpmailer/src/SMTP.php';
include '../phpmailer/src/Exception.php';

header('Content-Type: application/json');
ini_set('max_execution_time', 300);
class FileUploadHandler {
    private $db;
    private $uploadDirectory;
    private $flaskEndpoint;
    private $maxFileSize;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->uploadDirectory = '../pdf_files/';
        $this->flaskEndpoint = "http://127.0.0.1:3000/upload_research";
        $this->maxFileSize = 200 * 1024 * 1024;
        // Ensure upload directory exists

        if (!file_exists($this->uploadDirectory)) {
            mkdir($this->uploadDirectory, 0777, true);
        }
    }
    
    private function validateRequest() {
        $requiredFields = ['project_title', 'year', 'abstract', 'keywords', 'project_members'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        if (!isset($_FILES['project_file'])) {
            throw new Exception('No file uploaded');
        }
        
        $allowedTypes = [
            'application/pdf',
            'application/msword',                // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
        ];
        
        if (!in_array($_FILES['project_file']['type'], $allowedTypes)) {
            throw new Exception('Only PDF, DOC, and DOCX files are allowed');
        }
    
        if ($_FILES['project_file']['size'] > $this->maxFileSize) {
            $maxSizeMB = $this->maxFileSize / (1024 * 1024);
            throw new Exception("File size exceeds maximum limit of {$maxSizeMB} MB");
        }
    }
    
    private function handleFileUpload() {
        $uniqueFilename = uniqid() . '-' . $_FILES['project_file']['name'];
        $pdfPath = $this->uploadDirectory . $uniqueFilename;
        

        if (!move_uploaded_file($_FILES['project_file']['tmp_name'], $pdfPath)) {
            throw new Exception('Failed to move uploaded file');
        }
        
        return $pdfPath;
    }

    private function sendToFlaskServer($pdfPath) {
        $curlOpts = [
            CURLOPT_URL => $this->flaskEndpoint,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $this->preparePostFields($pdfPath),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_TCP_NODELAY => 1,  // Disable Nagle's algorithm
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_ENCODING => '',  // Accept all encodings
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, $curlOpts);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        return json_decode($response, true);
    }
    
    private function preparePostFields($pdfPath) {
        $randomNumber = mt_rand(1000000000, 9999999999); 
        date_default_timezone_set('Asia/Manila');
        
        // Determine file type based on extension
        $fileExtension = strtolower(pathinfo($pdfPath, PATHINFO_EXTENSION));
        $mimeType = 'application/pdf'; // default
        
        if ($fileExtension === 'doc') {
            $mimeType = 'application/msword';
        } elseif ($fileExtension === 'docx') {
            $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }
        
        return [
            'file' => new CURLFile($pdfPath, $mimeType, basename($pdfPath)),
            'archive_id' => $randomNumber,
            'student_id' => $_SESSION['auth_user']['student_id'],
            'project_title' => $_POST['project_title'],
            'date_of_submit' => date('d F Y'),
            'year' => $_POST['year'],
            'department_id' => $_SESSION['auth_user']['department_id'],
            'course_id' => $_SESSION['auth_user']['course_id'],
            'abstract' => $_POST['abstract'],
            'keywords' => $_POST['keywords'],
            'project_members' => $_POST['project_members'],
            'pdf_path' => $pdfPath,
            'owner_email' => $_SESSION['auth_user']['student_email']
        ];
    }
    
    public function process() {
        try {
            $this->validateRequest();
            $pdfPath = $this->handleFileUpload();
            $flaskResponse = $this->sendToFlaskServer($pdfPath);
            
            if ($flaskResponse['status'] === 'success') {
                $this->insertNotification($flaskResponse['archive_id']);

                $memberEmails = array_map('trim', explode(',', $_POST['project_members']));
                foreach ($memberEmails as $email) {
                    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $this->sendEmail($email, $_POST['project_title'], $flaskResponse['archive_id']);
                    }
                }
                return $this->prepareSuccessResponse($flaskResponse);
            } else {
                throw new Exception('Upload failed: ' . ($flaskResponse['message'] ?? 'Unknown error'));
            }
        } catch (Exception $e) {
            if (isset($pdfPath) && file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    private function sendEmail($email, $project_title, $archive_id) {
        $student_name = ucwords($_SESSION['auth_user']['student_name']);
        $subject = 'Collaboration with a Research Paper';
        $message = "
                <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                    <p>Dear $email,</p>
                    <p>Your collaboration with $student_name has submitted a research paper.</p>
                    <p>Log in or sign up to the system to view the paper.</p>
                    <a href='http://localhost:3000/student/view_project_research.php?archiveID=$archive_id' style='margin-top: 20px; text-align: center;'>View Paper</a>
                    <div style='margin: 20px 0; padding: 10px; background-color: #f5f5f5;'>
                        <p><strong>Archive ID:</strong> $archive_id</p>
                        <p><strong>Project Title:</strong> $project_title</p>
                    </div>
                </body>
                </html>
                ";

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'researcharchiverplagiarism@gmail.com';
        $mail->Password = 'wqjd ukqy plvb liyq';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('researcharchiverplagiarism@gmail.com');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if($mail->send()){
            return true;
        } else {
            return false;
        }

    }
    
    private function insertNotification($randomNumber) {
        $student_id = $_SESSION['auth_user']['student_id'];
        $student_no = $_SESSION['auth_user']['student_no'].$student_id;
        $student_name = $_SESSION['auth_user']['student_name'];
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully submitted your research paper.';
        
        $this->db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);
        
        $logs1 = $student_name.' submitted a new research paper.';
        $this->db->admin_Insert_NOTIFICATION($randomNumber, $logs1, $date, $time);  
    }
    
    private function prepareSuccessResponse($flaskResponse) {
        return [
            'stats' => 'Success',
            'status' => 'success',
            'message' => 'Research paper submitted successfully',
            'document_status' => $flaskResponse['document_status'],
            'archive_id' => $flaskResponse['archive_id'],
            'project_title' => $_POST['project_title'],
            'project_members' => $_POST['project_members'],
            'department' => $flaskResponse['department'],
            'date_published' => date('d F Y'),
            'project_abstract' => $_POST['abstract'],
        ];
    }
}

$handler = new FileUploadHandler(new Database());
echo json_encode($handler->process());