<?php
session_start();
include '../connection/config.php';

header('Content-Type: application/json');

class FileUploadHandler {
    private $db;
    private $uploadDirectory;
    private $flaskEndpoint;
    
    public function __construct(Database $db) {
        $this->db = $db;
        $this->uploadDirectory = '../pdf_files/';
        $this->flaskEndpoint = "http://127.0.0.1:3000/upload_research";
        
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
        
        if ($_FILES['project_file']['type'] !== 'application/pdf') {
            throw new Exception('Only PDF files are allowed');
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
            CURLOPT_TIMEOUT => 30,
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
        
        return [
            'file' => new CURLFile($pdfPath, 'application/pdf', basename($pdfPath)),
            'archive_id' => $randomNumber,
            'student_id' => $_SESSION['auth_user']['admin_uniqueID'],
            'project_title' => $_POST['project_title'],
            'date_of_submit' => date('d F Y'),
            'year' => $_POST['year'],
            'department_id' => $_POST['department'],
            'course_id' => $_POST['course'],
            'abstract' => $_POST['abstract'],
            'keywords' => $_POST['keywords'],
            'project_members' => $_POST['project_members'],
            'pdf_path' => $pdfPath,
            'owner_email' => $_POST['owner_email']
        ];
    }
    
    public function process() {
        try {
            $this->validateRequest();
            $pdfPath = $this->handleFileUpload();
            $flaskResponse = $this->sendToFlaskServer($pdfPath);
            
            if ($flaskResponse['status'] === 'success') {
                $this->insertNotification();
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
    
    private function insertNotification() {
        $admin_id = $_SESSION['auth_user']['admin_id'];
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You added a new research paper.';
        
        $this->db->admin_Insert_NOTIFICATION($admin_id, $logs, $date, $time);
    }
    
    private function prepareSuccessResponse($flaskResponse) {
        return [
            'stats' => 'Success',
            'status' => 'success',
            'message' => 'Added a new research paper',
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