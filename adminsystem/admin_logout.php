<?php 
include '../connection/config.php';
$db = new Database();

session_start();

if(isset($_SESSION['auth_user']['admin_id'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

date_default_timezone_set('Asia/Manila');
$date = date('F / d l / Y');
$time = date('g:i A');
$logs = 'You logged out.';
$online_offline_status = 'Offline';

$sql = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);

$sql2 = $db->updateADMIN_OFFLINE($online_offline_status, $admin_id);

}

session_destroy();

header("Location: index.php");

?>