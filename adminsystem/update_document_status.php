<?php
include '../connection/config.php';
$db = new Database();
if (isset($_POST['archiveID']) && isset($_POST['status'])) {
    $archiveID = $_POST['archiveID'];
    $status = $_POST['status'];

    $sql = $db->update_document_status($archiveID, $status);

    if ($sql) {
        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Research in not Accepted";
        $_SESSION['status-code'] = "success";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }
}
?>
