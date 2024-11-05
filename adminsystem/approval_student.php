<?php

include '../connection/config.php';
session_start();
$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_GET['studID'])) {
   $student_id = $_GET['studID'];

   $res = $db->update_student_school_verification($student_id);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Student approved";
    $_SESSION['status-code'] = "success";

    header('location: student_list.php');
}


?>