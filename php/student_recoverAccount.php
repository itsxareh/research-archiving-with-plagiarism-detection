<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();

if(ISSET($_POST['recover']) && isset($_SESSION['email'])){
    $otp_num = trim($_POST['code']);
    $email = $_SESSION['email'];

    $user = $db->student_profile_by_email($email);

    if($user['verification_code'] == $otp_num){
        header("location: ../student/change_password.php");
    } else {
        $_SESSION['alert'] = "Error!";
        $_SESSION['status'] = "Invalid Recovery Code";
        $_SESSION['status-code'] = "error"; 
        header("location: ../student/recover_account.php");
        exit();
    }

} else {
    $_SESSION['alert'] = "Error!";
    $_SESSION['status'] = "Invalid request";
    $_SESSION['status-code'] = "error";
    header("location:../student/login.php");
    exit();
}

?>