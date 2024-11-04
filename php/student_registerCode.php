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


if (isset($_POST['sign-up'])) {
    $first_name = $_POST['firstname'];
    // $middle_name = $_POST['middlename'];
    $last_name = $_POST['lastname'];
    $student_number = $_POST['snumber'];
    $department = $_POST['department'];
    $course = $_POST['course'];
    $PhoneNumber = $_POST['pnumber'];
    $email = trim($_POST['email']);
    $pword = $_POST['password'];
    $verification_code = rand(100000, 999999);

    $uniqueId = uniqid() . mt_rand(1000, 9999);
    
    $snumberPattern = '/^\d{3}-\d{5}[A-Za-z]$/';
    $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($snumberPattern, $_POST['snumber'])) {
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Invalid student number.";
        $_SESSION['status-code'] = "error";
        header("location: ../student/sign_up.php");
        exit();
    }
    if (!preg_match($passwordPattern, $pword)) {
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Password must be at least 8 characters long and include a mix of letters, numbers, & symbols.";
        $_SESSION['status-code'] = "error";
        header("location: ../student/sign_up.php");
        exit();
    }
    $user = $db->student_register_select_email($email);

    if ($user) {
        // Email already exists
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Email already exists";
        $_SESSION['status-code'] = "error";
        header("location: ../student/sign_up.php");
        exit();
    }

    $user = $db->student_register_select_StudentNumber($student_number);

    if ($user) {
        // Phone Number already exists
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Student No. already exists!";
        $_SESSION['status-code'] = "error";
        header("location: ../student/sign_up.php");
        exit();
    }
    
    if ($pword) {
        $sql = $db->student_register_INSERT_Info($department, $course, $student_number, $first_name, $last_name, $PhoneNumber, $email, $pword, '', $verification_code);

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
        $mail->send();

        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Kindly check your registered email for account verification code.";
        $_SESSION['status-code'] = "success";
        header("location: ../student/student_verify_account.php?student_no={$student_number}");
    } else {
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Failed to move image file.";
        $_SESSION['status-code'] = "error";
        header("location: ../student/sign_up.php");
    }
} else {
    $_SESSION['alert'] = "Oppss...";
    $_SESSION['status'] = "PASSWORD NOT MATCH";
    $_SESSION['status-code'] = "error";
    header("location: ../student/sign_up.php");
}
?>
