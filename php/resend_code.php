<?php 
include '../connection/config.php';
$db = new Database();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../phpmailer/src/PHPMailer.php';
include '../phpmailer/src/SMTP.php';

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
		
if(isset($_SESSION['email']) || isset($_POST['email'])){
    $email = isset($_POST['email']) ? $_POST['email'] : $_SESSION['email'];
    $verification_code = rand(100000, 999999);


    $sql = $db->recover_code_with_email($email, $verification_code);

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
    $mail->Body = 'Your account OTP code is <strong> ' . $verification_code . '.</strong> Please use this code to recover your account.';
    $mail->send();

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "OTP sent.";
    $_SESSION['status-code'] = "success";
    header("location: ../student/recover_account.php");
    exit();

} else {
    $_SESSION['alert'] = "Error!";
    $_SESSION['status'] = "Invalid request";
    $_SESSION['status-code'] = "error";
    header("location:../student/login.php");
    exit();
}

?>

