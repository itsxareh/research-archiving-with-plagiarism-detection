<?php

include '../connection/config.php';
$db = new Database();

error_reporting(0);

session_start();


if(ISSET($_POST['verify'])){
    $otp_num = $_POST['verification_number'];
    $admin_id = $_POST['admin_id'];
    $verified = 'Verified';
    $user = $db->admin_profile_by_id($admin_id);
    if ($user) {
        $admin_UNIQUEid = $user['uniqueID'];
        $admin_email = $user['admin_email']; 
        $pword = $user['admin_password'];
        $verifystatus = $user['verify_status'];
        $admin_status = $user['admin_status'];
        $role_id = $user['role_id'];
    } else {
        $_SESSION['alert'] = "Error!";
        $_SESSION['status'] = "Please contact the administrator for assistance.";
        $_SESSION['status-code'] = "error"; 
        exit();
    }
    if($user["verification_code"]==$otp_num){
        $stmt = $db->admin_update_verify_status($verified, $admin_id);

        $userRole = $db->getRoleById($user['role_id']);
        if ($userRole) {
            $role_status = $userRole['role_status'];
        } else {
            $_SESSION['alert'] = "Error!";
            $_SESSION['status'] = "Please contact the administrator for assistance.";
            $_SESSION['status-code'] = "error"; 
            exit();
        }

        if ($role_status != 'Active') {
            $_SESSION['alert'] = "Error!";
            $_SESSION['status'] = "Please contact the administrator for assistance.";
            $_SESSION['status-code'] = "error"; 
            exit();
        }
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You logged in.';
        $online_offline_status = 'Online';
        
        $sql = $db->admin_Insert_NOTIFICATION($admin_id, $logs, $date, $time);
        $sql2 = $db->updateADMIN_OFFLINE($online_offline_status, $admin_id);

        $_SESSION['alert'] = "Success!";
        $_SESSION['status'] = "Your account is now verified.";
        $_SESSION['status-code'] = "success"; 

        $_SESSION['auth'] = true;
        $_SESSION['auth_user'] = [
            'admin_id' => $admin_id,
            'admin_uniqueID' => $admin_UNIQUEid,
            'admin_email' => $admin_email,
            'role_id' => $role_id,
        ];
        
        $permissions = explode(',', $userRole['permissions']);
        function hasPermit($permissions, $permissionToCheck) {
            foreach ($permissions as $permission) {
                if (strpos($permission, $permissionToCheck) === 0) {
                    return true;
                }
            }
            return false;
        }
        if ($_SESSION['auth_user']['admin_id'] == 0) {
            echo "<script>window.location.href='../adminsystem/index.php'</script>";
            exit();
        } elseif (hasPermit($permissions, 'dashboard_view')) {
            header("Location: ../adminsystem/dashboard.php");
        } elseif (hasPermit($permissions, 'student_list_view')) {
            header("Location: ../adminsystem/students.php");
        } elseif (hasPermit($permissions, 'research_view')) {
            header("Location: ../adminsystem/all_project_list.php");
        } elseif (hasPermit($permissions, 'department_view')) {
            header("Location: ../adminsystem/departments.php");
        } elseif (hasPermit($permissions, 'course_view')) {
            header("Location: ../adminsystem/courses.php");
        } elseif (hasPermit($permissions, 'role_view')) {
            header("Location: ../adminsystem/roles.php");
        } elseif (hasPermit($permissions, 'user_view')) {
            header("Location: ../adminsystem/admins.php");
        } else {
            header('Location: ../bad-request.php');
        }
        exit();
    } else {
        $_SESSION['alert'] = "Error!";
        $_SESSION['status'] = "Invalid Verification Code";
        $_SESSION['status-code'] = "error"; 
        header("location: ../adminsystem/admin_verify_account.php?id=$admin_id");
    }
}

?>