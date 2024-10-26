<?php
include '../connection/config.php';
session_start();
$db = new Database();
if (isset($_POST['departmentID']) && isset($_POST['status'])) {
    $departmentID = $_POST['departmentID'];
    $status = $_POST['status'];

    $sql = $db->update_department_status($departmentID, $status);

    if ($sql) {
        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Department is now ".strtoupper($status);
        $_SESSION['status-code'] = "success";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
}
?>
