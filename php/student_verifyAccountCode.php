<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();

if(ISSET($_POST['verifyNow'])){
		
    $otp_num = $_POST['verification_number'];
    $student_id = $_POST['studentID'];
    $verified = 'Verified';

    $user = $db->student_profile($student_id);

if($user["verification_code"]==$otp_num)
{

$stmt = $db->student_update_verify_status($verified, $student_id);

$_SESSION['alert'] = "Success!";
$_SESSION['status'] = "Student Account Verified. Log In Again.";
$_SESSION['status-code'] = "success"; 
header("location: ../student/index.php");
}


else {
    $_SESSION['alert'] = "Error!";
    $_SESSION['status'] = "Wrong Verification Number";
    $_SESSION['status-code'] = "error"; 
    header("location: ../student/student_verify_account.php?id=$student_id");
}




}

?>