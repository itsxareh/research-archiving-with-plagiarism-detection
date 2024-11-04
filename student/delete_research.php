<?php

include '../connection/config.php';
session_start();

$db = new Database();


//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['archiveID'])){

    $archive_id = $_GET['archiveID'];

    $sql1 = $db->delete_research($archive_id);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Research deleted successfully";
    $_SESSION['status-code'] = "success";

    header('location: project_list.php');
    
    
}