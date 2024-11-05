<?php

include '../connection/config.php';
session_start();
$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_GET['studID'])) {
   $student_id = $_GET['studID'];

   $set_block = "Blocked";
   $res = $db->block_student_school_verification($student_id, $set_block);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Student blocked";
    $_SESSION['status-code'] = "success";

    header('location: student_list.php');
}


?>