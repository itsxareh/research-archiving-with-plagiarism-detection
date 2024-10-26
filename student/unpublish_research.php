<?php

include '../connection/config.php';
session_start();

$db = new Database();


//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if(isset($_GET['archiveID'])){

    $archiveID = $_GET['archiveID'];

    $sql1 = $db->unpublish_research($archiveID);

    $_SESSION['alert'] = "Success";
    $_SESSION['status'] = "Research in now UNPUBLISHED";
    $_SESSION['status-code'] = "success";

    header('location: archive_list.php');
    
    
}