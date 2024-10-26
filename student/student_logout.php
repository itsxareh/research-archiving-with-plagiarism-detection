<?php 
include '../connection/config.php';
$db = new Database();

session_start();

if(isset($_SESSION['auth_user']['student_id'])){

    $student_id = $_SESSION['auth_user']['student_id'];

date_default_timezone_set('Asia/Manila');
$date = date('F / d l / Y');
$time = date('g:i A');
$logs = 'You successfully logged out to your account.';

$sql = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);

}

session_destroy();

header("Location: login.php");

?>