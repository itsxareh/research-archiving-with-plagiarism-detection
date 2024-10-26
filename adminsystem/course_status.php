<?php

include '../connection/config.php';
session_start();

$db = new Database();


//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['courseID'])){

    $courseID = $_GET['courseID'];

    $sql1 = $db->unactivate_course($courseID);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Course is now unactivated";
    $_SESSION['status-code'] = "success";

    header('location: course_list.php');
    
    
}

if(isset($_GET['courseID_activate'])){

    $courseID_activate = $_GET['courseID_activate'];

    $sql1 = $db->ACTIVATE_course($courseID_activate);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Course is now ACTIVATED";
    $_SESSION['status-code'] = "success";

    header('location: course_list.php');
    
    
}


?>