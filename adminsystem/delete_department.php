<?php

include '../connection/config.php';
session_start();

$db = new Database();

if (!isset($_SESSION['auth_user']['admin_id'])){
    header('location: ../bad-request.php');
    exit();
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['departmentID'])){

    $department_id = $_GET['departmentID'];
    $res = $db->department_profile($department_id);
    if ($res){
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You deleted '.$res['name'].' department.';
    
        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);
    }
    $sql1 = $db->delete_department($department_id);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Department deleted";
    $_SESSION['status-code'] = "success";

    header('location: departments.php');
    
    
}