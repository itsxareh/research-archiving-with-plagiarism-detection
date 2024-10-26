<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();

if(ISSET($_POST['verifyNow'])){
		
    $otp_num = $_POST['verification_number'];
    $adminID = $_POST['adminid'];
    $verified = 'Verified';

    $user = $db->admin_profile($adminID);

if($user["verification_code"]==$otp_num)
{

$stmt = $db->admin_update_verify_status($verified, $adminID);

$_SESSION['alert'] = "Success!";
$_SESSION['status'] = "Admin Account Verified. Log In Again.";
$_SESSION['status-code'] = "success"; 
header("location: ../adminsystem/index.php");
}


else {
    $_SESSION['alert'] = "Error!";
    $_SESSION['status'] = "Wrong Verification Number";
    $_SESSION['status-code'] = "error"; 
    header("location: ../adminsystem/admin_verify_account.php?id=$adminID");
}




}

?>