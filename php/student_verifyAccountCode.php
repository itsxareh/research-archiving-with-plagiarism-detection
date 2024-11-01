<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();

if(ISSET($_POST['verify'])){
    $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
    $otp_num = $_POST['verification_number'];
    $student_id = $_POST['student_no'];
    $verified = 'Verified';

    $user = $db->student_profile_by_sno($student_id);

    if($user["verification_code"]==$otp_num){
        $stmt = $db->student_update_verify_status($verified, $student_id);

        $_SESSION['alert'] = "Success!";
        $_SESSION['status'] = "Your account is now verified.";
        $_SESSION['status-code'] = "success"; 

        $_SESSION['auth'] = true;
        $_SESSION['auth_user'] = [
            'student_id' => $user['aid'],
            'student_email' => $user['student_email'],
            'department_id' => $user['department_id'],
            'course_id' => $user['course_id'],
        ];
        if (isset($redirect_to) || !empty($redirect_to) || $redirect_to !== '') {
            $redirect_url = urldecode($redirect_to);
            header("location: $redirect_url");
        } 
        if (!isset($redirect_to) || empty($redirect_to)) {
            header("location: ../student/all_project_list.php");
        }
    } else {
        $_SESSION['alert'] = "Error!";
        $_SESSION['status'] = "Invalid Verification Code";
        $_SESSION['status-code'] = "error"; 
        header("location: ../student/student_verify_account.php?student_no=$student_id");
    }

}

?>