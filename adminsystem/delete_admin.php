<?php

include '../connection/config.php';
session_start();

$db = new Database();

if (!isset($_SESSION['auth_user']['admin_id']) || $_SESSION['auth_user']['admin_type'] == 1){
    header('location: ../bad-request.php');
    exit();
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['adminID'])){

    $adminID = $_GET['adminID'];
    $res = $db->admin_profile($adminID);
    if ($res){
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You deleted an account of '.$res['first_name'] .' '. $res['last_name'].'.';
    
        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);
    }
    $sql1 = $db->delete_admin($adminID);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Admin deleted";
    $_SESSION['status-code'] = "success";

    header('location: admins.php');
    
    
}