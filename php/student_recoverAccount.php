<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();

if(isset($_SESSION['email'])){
    $otp_num = trim($_POST['code']);
    $email = $_SESSION['email'];

    $user = $db->student_profile_by_email($email);

    if($user['verification_code'] == $otp_num){
        echo json_encode(array("status_code" => "success", "status" => "Success", "redirect" => "../student/change_password.php"));
    } else {
        echo json_encode(array("status_code" => "invalid", "status" => "Invalid recovery code", "redirect" => "../student/recover_account.php"));
    }

} else {
    echo json_encode(array("status_code" => "error", "status" => "Invalid request", "redirect" => "../student/login.php"));
}

?>