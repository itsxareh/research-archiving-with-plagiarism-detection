<?php
include '../connection/config.php';
session_start();
$db = new Database();
if (isset($_POST['courseID']) && isset($_POST['status'])) {
    $courseID = $_POST['courseID'];
    $status = $_POST['status'];

    $sql = $db->update_course_status($courseID, $status);

    if ($sql) {
        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Course is now ".strtoupper($status);
        $_SESSION['status-code'] = "success";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
}
?>
