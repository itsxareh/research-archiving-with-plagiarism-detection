<?php

include '../connection/config.php';
session_start();

$db = new Database();


//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['archiveID'])){

    $archiveID = $_GET['archiveID'];

    $sql1 = $db->publish_research($archiveID);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Research in now PUBLISHED";
    $_SESSION['status-code'] = "success";

    header('location: research-papers.php');
    
    
}