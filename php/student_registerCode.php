<?php

include '../connection/config.php';
$db = new Database();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../phpmailer/src/PHPMailer.php';
include '../phpmailer/src/SMTP.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();


if (isset($_POST['snumber']) && isset($_POST['password']) && isset($_POST['email'])) {
    $first_name = trim($_POST['firstname']);
    // $middle_name = $_POST['middlename'];
    $last_name = trim($_POST['lastname']);
    $student_number = trim($_POST['snumber']);
    $department = trim($_POST['department']);
    $course = trim($_POST['course']);
    $PhoneNumber = trim($_POST['pnumber']);
    $email = trim($_POST['email']);
    $pword = trim($_POST['password']);
    $verification_code = rand(100000, 999999);

    $uniqueId = uniqid() . mt_rand(1000, 9999);
    
    $snumberPattern = '/^\d{3}-\d{5}[A-Za-z]$/';
    $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($snumberPattern, $_POST['snumber'])) {
        echo json_encode(array('status_code' => 'error', 'status' => 'Invalid student number.', 'alert' => 'Oppss...'));
        exit();
    }
    if (!preg_match($passwordPattern, $pword)) {
        echo json_encode(array('status_code' => 'error', 'status' => 'Password must be at least 8 characters long and include a mix of letters, numbers, & symbols.', 'alert' => 'Oppss...'));
        exit();
    }
    $user = $db->student_register_select_email($email);

    if ($user) {
        echo json_encode(array('status_code' => 'error', 'status' => 'Email already exists', 'alert' => 'Oppss...'));
        exit();
    }

    $user = $db->student_register_select_StudentNumber($student_number);

    if ($user) {
        echo json_encode(array('status_code' => 'error', 'status' => 'Student no. already exists!', 'alert' => 'Oppss...'));
        exit();
    }
    
    if ($pword) {
        $sql = $db->student_register_INSERT_Info($department, $course, $student_number, $first_name, $last_name, $PhoneNumber, $email, $pword, '', $verification_code);
        $un = $db->getConnection()->lastInsertId();
            
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
        $mail->Subject = 'Account Verification Code';
        $mail->Body = 'Your account OTP code is <strong> ' . $verification_code . '.</strong> Please use this code to verify your account.';
        if ($mail->send()) {
            
            date_default_timezone_set('Asia/Manila');
            $date = date('F / d l / Y');
            $time = date('g:i A');
            $logs = 'You successfully registered an account.';

            $sql1 = $db->student_Insert_NOTIFICATION($un, $logs, $date, $time);

            echo json_encode(array('status_code' => 'success', 'status' => 'Kindly check your registered email for account verification code.', 'redirect' => '../student/student_verify_account.php?student_no='.$student_number));
            exit();
            
        } else {
            echo json_encode(array('status_code' => 'error', 'status' => 'Failed to send email.', 'alert' => 'Oppss...'));
            exit();
        }
    } else {
        echo json_encode(array('status_code' => 'error', 'status' => 'Failed to send email.', 'alert' => 'Oppss...'));
        exit();
    }
} else {
    echo json_encode(array('status_code' => 'error', 'status' => 'Password not match', 'alert' => 'Oppss...'));
    exit();
}
?>
