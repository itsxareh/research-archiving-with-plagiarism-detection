<?php
session_start();
include '../connection/config.php';
$db = new Database();

if (!isset($_SESSION['auth_user'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to request access.'
    ]);
    exit();
}

$user_id = $_SESSION['auth_user']['student_no'] ?? $_SESSION['auth_user']['admin_uniqueID'];
$user_type = isset($_SESSION['auth_user']['student_no']) ? 'student' : ($_SESSION['auth_user']['role_id'] == 1 ? 'superadmin' : 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive_id']) && isset($_POST['request_reason'])) {
    $archive_id = $_POST['archive_id'];
    $request_reason = htmlspecialchars($_POST['request_reason']);
    
    if ($user_type === 'superadmin') {
        echo json_encode([
            'status' => 'As an admin, you have been granted access automatically.',
            'message' => 'success',
            'access' => 'granted',
            'status_code' => 'Success'
        ]);
        exit();
    }
    
    if ($db->INSERT_ACCESS_REQUEST($archive_id, $user_id, $request_reason)) {
        echo json_encode([
            'status' => 'Your access request has been submitted and is pending approval.',
            'message' => 'success',
            'status_code' => 'Success'
        ]);
    } else {
        echo json_encode([
            'status' => 'There was an error submitting your request. Please try again.',
            'message' => 'warning',
            'status_code' => 'Error occured'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'Invalid request. Please provide all required information.',
        'message' => 'warning',
        'status_code' => 'Error occured'
    ]);
}
?>