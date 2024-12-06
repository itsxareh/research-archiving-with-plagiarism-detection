<?php

include '../connection/config.php';
session_start();
$db = new Database();

$admin_id = $_SESSION['auth_user']['admin_id'];
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_GET['studID'])) {
   $student_id = $_GET['studID'];

   $res = $db->update_student_school_verification($student_id);

   if ($res){
    date_default_timezone_set('Asia/Manila');
    $date = date('F / d l / Y');
    $time = date('g:i A');
    $logs = 'You '.strtolower($res['school_verify']).' '.$res['first_name'] .' '. $res['last_name'].'.';

    $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);
}
    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Student approved";
    $_SESSION['status-code'] = "success";

    header('location: students.php');
}


?>