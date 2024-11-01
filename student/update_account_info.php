<?php
include '../connection/config.php';
$db = new Database();
session_start();

if (isset($_POST['first_name']) && isset($_POST['middle_name']) && isset($_POST['last_name'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $department = $_POST['department'];
    $course = $_POST['department_course'];

    date_default_timezone_set('Asia/Manila');
    $date = date('F / d l / Y');
    $time = date('g:i A');
    $logs = 'You successfully updated your information.';

    $stmt = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);

    if ($stmt) {
        $full_name = "$fname $mname $lname";
        $response = [
            'status' => 'success',
            'message' => 'Profile updated successfully.',   
            'full_name' => $full_name,
            'student_id' => $student_id,
            'course' => $course, 
            'department' => $department,
        ];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to update profile.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Incomplete data.'];
}

echo json_encode($response);
?>