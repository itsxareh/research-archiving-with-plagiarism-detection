<?php
include '../connection/config.php';
session_start();

$db = new Database();

if (isset($_SESSION['auth_user']['student_id'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $department = $_SESSION['auth_user']['department_id'];
    $course = $_SESSION['auth_user']['course_id'];

    $currentData = $db->student_profile($student_id);

    if ($currentData) {

        $stmt = $db->UPDATE_student_info_onSETTINGS($fname, $mname, $lname, $department, $course, $student_id);
        
        $response = array(
            'status' => 'success',
            'stats' => 'Success',
            'message' => 'Updated successfully',
            'first_name' => $fname,
            'middle_name' => $mname,
            'last_name' => $lname
        );
    } else {
        $response = array(
            'status' => 'info',
            'stats' => 'No changes',
            'message' => 'No updates made. The information is already up to date.',
        );
    }
    echo json_encode($response);
} else {
    echo json_encode(array('status' => 'error','message' => 'Incomplete data.'));
}

?>