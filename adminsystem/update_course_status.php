<?php
include '../connection/config.php';
session_start();
$db = new Database();

$admin_id = $_SESSION['auth_user']['admin_id'];

if (isset($_POST['courseID']) && isset($_POST['status'])) {
    $courseID = $_POST['courseID'];
    $status = $_POST['status'];

    $sql = $db->update_course_status($courseID, $status);

    if ($sql) {
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You changed the status of '. $sql['course_name'] .' to '.strtolower($status).'.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);

        echo json_encode(['alert' => 'Success', 'statusCode' => 'success', 'status' => 'Course is now '.strtolower($status)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
}
?>
