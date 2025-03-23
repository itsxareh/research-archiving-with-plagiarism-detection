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

// Get user ID (either student_no or admin_id)
$user_id = $_SESSION['auth_user']['student_no'] ?? $_SESSION['auth_user']['admin_uniqueID'];
$user_type = isset($_SESSION['auth_user']['student_no']) ? 'student' : 'admin';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['archive_id']) && isset($_POST['request_reason'])) {
    $archive_id = $_POST['archive_id'];
    $request_reason = htmlspecialchars($_POST['request_reason']);
    
    // If it's an admin, automatically grant access
    if ($user_type === 'admin') {
        // You might want to log this access or handle it differently
        echo json_encode([
            'status' => 'success',
            'message' => 'As an admin, you have been granted access automatically.',
            'access' => 'granted'
        ]);
        exit();
    }
    
    // For students, insert the access request
    // Assuming you've fixed the database methods according to your DB class implementation
    if ($db->INSERT_ACCESS_REQUEST($archive_id, $user_id, $request_reason)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Your access request has been submitted and is pending approval.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'There was an error submitting your request. Please try again.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request. Please provide all required information.'
    ]);
}
?>
