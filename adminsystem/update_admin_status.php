<?php
include '../connection/config.php';
session_start();
$db = new Database();
$admin_id = $_SESSION['auth_user']['admin_id'];

if (isset($_POST['adminID']) && isset($_POST['status'])) {
    $adminID = $_POST['adminID'];
    $status = $_POST['status'];

    $sql = $db->update_admin_status($adminID, $status);

    if ($sql) {
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You changed the status of '. $sql['first_name'] .' '. $sql['last_name'] .' to '.strtolower($status).'.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);
    
        echo json_encode(['alert' => 'Success', 'statusCode' => 'success', 'status' => 'Admin account is now '.strtolower($status)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
}
?>
