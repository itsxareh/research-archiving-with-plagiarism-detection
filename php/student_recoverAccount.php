<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();

if(ISSET($_POST['recover'])){
    $otp_num = trim($_POST['code']);
    $email = $_POST['email'];

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

}

?>