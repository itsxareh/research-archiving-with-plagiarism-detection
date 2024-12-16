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


if (isset($_POST['email'])) {

    $email = trim($_POST['email']);
    $verification_code = rand(100000, 999999);

    $user = $db->student_forgot_select_email($email);

    if ($user) {

        $sql = $db->student_forgot_account($email, $verification_code);

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
        $mail->Subject = 'Recover Account Code';
        $mail->Body = 'Your account OTP code is <strong> ' . $verification_code . '.</strong> Please use this code to recover your account.';
        $mail->send();

        echo json_encode(array("status_code" => "success", "status" => "Please check your registered email for OTP code to recover your account.", "alert" => "Success", "redirect" => "../student/recover_account.php"));
    } else {
        echo json_encode(array("status_code" => "error", "status" => "Sorry, we couldn't find your account. Please try again.", "alert" => "Oppss..."));
    }
} else {
    echo json_encode(array("status_code" => "error", "status" => "Invalid", "alert" => "Oppss..."));
}
?>
