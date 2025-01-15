<?php 
$db = new Database();
session_start();
date_default_timezone_set('Asia/Manila');
$current_date_time = date('Y-m-d H:i:s A');

function hasPermit($permissions, $permissionToCheck) {
    foreach ($permissions as $permission) {
        if (strpos($permission, $permissionToCheck) === 0) {
            return true;
        }
    }
    return false;
}
$userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
$departmentId = $userRole['department_id'];
$permissions = explode(',', $userRole['permissions']);
if (!hasPermit($permissions, 'dashboard_view')) {
    header('Location:../../bad-request.php');
    exit();
} elseif ($_SESSION['auth_user']['admin_id']==0){
    header('Location:../../bad-request.php');
    exit();
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $filterByDepartment = ($departmentId != 0);
}


?>