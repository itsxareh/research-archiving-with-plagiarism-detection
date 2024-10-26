<?php

include '../connection/config.php';
session_start();

$db = new Database();


//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['departmentID'])){

    $departmentID = $_GET['departmentID'];

    $sql1 = $db->unactivate_department($departmentID);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Department is now unactivated";
    $_SESSION['status-code'] = "success";

    header('location: department_list.php');
    
    
}

if(isset($_GET['departmentID_activate'])){

    $departmentID = $_GET['departmentID_activate'];

    $sql1 = $db->ACTIVATE_department($departmentID);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Department is now ACTIVATED";
    $_SESSION['status-code'] = "success";

    header('location: department_list.php');
    
    
}


?>