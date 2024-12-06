<?php

include '../connection/config.php';

$db = new Database();

session_start();
$userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
$permissions = explode(',', $userRole['permissions']);

// Helper function to check permissions
function hasPermit($permissions, $permissionToCheck) {
    foreach ($permissions as $permission) {
        if (strpos($permission, $permissionToCheck) === 0) {
            return true;
        }
    }
    return false;
}
if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    exit(); 
    
} elseif(!hasPermit($permissions, 'role_view')) {
    header('Location:../../bad-request.php');
    exit(); 
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}

if (isset($_POST['add_role'])) {
    $role_name = $_POST['role_name'];
    $description = $_POST['description'];
    $department_id = $_POST['department_id'];
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    
    // Process permissions based on department selection
    $processed_permissions = [];
    foreach ($permissions as $permission) {
        // If department is not 'all', add department context to research and student permissions
        if ($department_id !== 0 && (strpos($permission, 'research_') === 0 || strpos($permission, 'student_list_') === 0)) {
            $processed_permissions[] = $permission . ':' . $department_id;
        } else {
            $processed_permissions[] = $permission . ':1';
        }
    }
    
    // Convert processed permissions array to string
    $permissions_string = implode(',', $processed_permissions);

    // Insert role into the database
    $sql = $db->insert_Role($role_name, $description, $department_id, $permissions_string);

    if ($sql) {
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You added a new role: ' . $role_name;

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);
        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Role added successfully";
        $_SESSION['status-code'] = "success";
        
    } else {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Failed to add role";
        $_SESSION['status-code'] = "error";
    }
    header("location: roles.php");
    exit();
    
}


if (isset($_POST['edit_role'])) {
    $role_id = $_POST['role_id'];
    $role_name = $_POST['role_name'];
    $description = $_POST['description'];
    $department_id = $_POST['department_id'];
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];
    
    // Process permissions based on department selection
    
    foreach ($permissions as $permission) {
        // If department is not 'all', add department context to research and student permissions
        if ($department_id !== 0 && (strpos($permission, 'research_') === 0 || strpos($permission, 'student_list_') === 0)) {
            $processed_permissions[] = $permission . ':' . $department_id;
        } else {
            $processed_permissions[] = $permission . ':1';
        }
    }
    
    // Convert processed permissions array to string
    $permissions_string = implode(',', $processed_permissions);
    // Update role in the database
    $sql = $db->update_Role($role_id, $role_name, $description, $department_id, $permissions_string);

    if ($sql) {
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You updated role: ' . $role_name;

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);
        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Role updated successfully";
        $_SESSION['status-code'] = "success";
    } else {
        $_SESSION['alert'] = "Error";
        $_SESSION['status'] = "Failed to update role";
        $_SESSION['status-code'] = "error";
    }
    // header("location: roles.php");
    // exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- theme meta -->
    <meta name="theme-name" content="focus" />
    <title>Role List: EARIST Repository</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo2.webp">
    <!-- Retina iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <!-- Retina iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <!-- Standard iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <!-- Standard iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">
    <!-- Styles -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="../css/action-dropdown.css" rel="stylesheet">

    <!---------------------DATATABLES------------------------->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->


<?php
if (hasPermission($permissions, 'role_view')):
?>
    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="col-lg-12 title-page">
                <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12  flex justify-content-between align-items-center page-title">
                                <h1 style="display: flex; ">Role</h1>
                                <?php if (hasPermission($permissions, 'role_download')): ?>
                                <div class="generate-report ">
                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_roles" class="btn print-button">
                                        Print
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <?php if (hasPermission($permissions, 'role_add')): ?>
                <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add a role</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row m-0" style="justify-content: space-between; row-gap: 6px">
                                            <div class="col-sm-6 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Role name</label>
                                                <input type="text" class="info-input" name="role_name" required>
                                            </div>
                                                <div class="col-sm-5 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Description</label>
                                                <input type="text" class="info-input" name="description" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 item-detail p-0">
                                            <label for="" class="info-label m-l-4">Department</label>
                                            <select class="info-input" name="department_id" id="department" required>
                                                <option value="" selected disabled>Select Department</option>
                                                <option value="0">All</option>
                                            <?php 
                                                $res = $db->showDepartments_WHERE_ACTIVE();

                                                foreach ($res as $item) {
                                                echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                                                }
                                            ?>
                                            </select>
                                            <small class="permissions-help-text text-muted m-l-4">
                                                Select a department to continue
                                            </small>
                                        </div>
                                        <div class="row m-r-0 m-l-0 mt-3"> 
                                            <div class="col-12 p-0"> 
                                                <label class="info-label m-l-4">Permissions</label> 
                                                <div class="permissions-list">
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="dashboard">
                                                            <h6>Dashboard Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="dashboard_view">
                                                                <label>View Dashboard</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="dashboard_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="research_paper">
                                                            <h6>Research Paper Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="research_view">
                                                                <label>View Research Papers</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="research_add">
                                                                <label>Add Research Papers</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="research_edit">
                                                                <label>Edit Research Papers</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="research_delete">
                                                                <label>Delete Research Papers</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="research_publish">
                                                                <label>Publish Research Papers</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="research_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="student_list">
                                                            <h6>Student List Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="student_list_view">
                                                                <label>View Student</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="student_list_edit">
                                                                <label>Edit Student</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="student_list_delete">
                                                                <label>Delete Student</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="student_list_status">
                                                                <label>Change Status</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="student_list_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div> 
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row m-r-0 m-l-0 mt-3"> 
                                            <div class="col-12 p-0"> 
                                                <label class="info-label m-l-4">Other Permissions</label> 
                                                <div class="permissions-list">
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="department">
                                                            <h6>Department Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="department_view">
                                                                <label>View Department</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="department_add">
                                                                <label>Add Department</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="department_edit">
                                                                <label>Edit Department</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="department_delete">
                                                                <label>Delete Department</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="department_status">
                                                                <label>Change Status</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="department_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="course">
                                                            <h6>Course Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="course_view">
                                                                <label>View Course</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="course_add">
                                                                <label>Add Course</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="course_edit">
                                                                <label>Edit Course</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="course_delete">
                                                                <label>Delete Course</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="course_status">
                                                                <label>Change Status</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="course_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="role">
                                                            <h6>Role Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="role_view">
                                                                <label>View Role</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="role_add">
                                                                <label>Add Role</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="role_edit">
                                                                <label>Edit Role</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="role_delete">
                                                                <label>Delete Role</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="role_status">
                                                                <label>Change Status</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="role_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="permission-group">
                                                        <div class="permission-group-header">
                                                            <input type="checkbox" name="permissions[]" value="user">
                                                            <h6>User Access</h6>
                                                        </div>
                                                        <div class="permission-group-items">
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_view">
                                                                <label>View User</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_add">
                                                                <label>Add User</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_edit">
                                                                <label>Edit User</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_delete">
                                                                <label>Delete User</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_logs">
                                                                <label>User Logs</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_status">
                                                                <label>Change Status</label>
                                                            </div>
                                                            <div class="permission-item">
                                                                <input type="checkbox" name="permissions[]" value="user_download">
                                                                <label>Download PDF</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button name="add_role" class="btn btn-danger">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-md-12 ">
                    <!-- Button trigger modal -->
                    <?php if (hasPermission($permissions, 'role_add')): ?>
                    <div class="add-department">
                        <button type="button" class="add-research-button item-meta" data-toggle="modal" data-target="#addRoleModal">
                        <i class="ti-plus m-r-4"></i> Add a role
                        </button>
                    </div>
                    <?php endif; ?>
                <div class="list-container">
                    <table id="datatablesss" class="table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="list-th">Name</th>
                                <th class="list-th">Department</th>
                                <th class="list-th">Description</th>
                                <th class="list-th">Status</th>
                                <th class="list-th"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            function normalizePermission($permission) {
                                // Remove the :1 or :{department_id} suffix if present
                                return preg_replace('/:.*$/', '', $permission);
                            }
                            $data = $db->showRoles();
                            if (count($data) != 0) {
                                
                            foreach ($data as $result) {
                                $existingPermissions = array_map('normalizePermission', explode(',', $result['permissions']));
                            ?>
                            <tr>
                                <td class="list-td"><?= $result['role_name'] ?></td>
                                <td class="list-td"><?= ($result['department_id'] == 0) ? 'All' : $result['name'] ?></td>
                                <td class="list-td"><?= $result['role_description'] ?></td>
                                <td class="list-td" style="text-align: center;">
                                    <label class="switch">
                                    <input 
                                        type="checkbox" 
                                        class="toggle-status" 
                                        data-id="<?= $result['roleID'] ?>" 
                                        data-toggle="toggle" 
                                        data-on="Accept" 
                                        data-off="Don't Accept" 
                                        data-onstyle="success" 
                                        data-offstyle="danger"
                                        <?= ($result['role_status'] === 'Active') ? 'checked' : '' ?>
                                        <?php if (!hasPermission($permissions, 'role_status')): ?>
                                            disabled
                                        <?php endif; ?>
                                    >
                                    <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="list-td" style="text-align: center;">
                                    <div class="action-container">
                                        <div>
                                            <button type="button" class="action-button"  id="action-button_<?= $result['roleID'] ?>" aria-expanded="true" aria-haspopup="true">
                                                Action
                                                <svg class="action-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <?php if ($result['roleID'] != 1): ?>
                                        <div class="dropdown-action" id="dropdown_<?= $result['roleID'] ?>" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                            <div role="none">
                                                <?php if(hasPermit($permissions, 'role_edit')): ?>
                                                    <a href="#" data-toggle="modal" data-target="#editRoleModal_<?= $result['roleID'] ?>" class="dropdown-action-item">Edit role</a>
                                                <?php endif; ?>
                                                <?php if(hasPermit($permissions, 'role_delete')): 
                                                        ?>
                                                        <a href="#" onclick="confirmDelete(<?= $result['roleID'] ?>)" class="dropdown-action-item">Delete role</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php 
                                if (hasPermission($permissions, 'role_edit')):
                                ?>
                                <div class="modal fade" id="editRoleModal_<?= $result['roleID'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-md" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Role</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="" method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="role_id" value="<?= $result['roleID'] ?>">
                                                    <div class="form-group">
                                                        <div class="row m-0" style="justify-content: space-between; row-gap: 6px">
                                                            <div class="col-sm-6 item-detail p-0">
                                                                <label class="info-label m-l-4">Role name</label>
                                                                <input type="text" class="info-input" name="role_name" value="<?= $result['role_name'] ?>" required>
                                                            </div>
                                                            <div class="col-sm-5 item-detail p-0">
                                                                <label class="info-label m-l-4">Description</label>
                                                                <input type="text" class="info-input" name="description" value="<?= $result['role_description'] ?>" required>
                                                            </div>
                                                        </div>
                                                        <!-- Department selection -->
                                                        <div class="col-sm-12 item-detail p-0">
                                                            <label class="info-label m-l-4">Department</label>
                                                            <select class="info-input" name="department_id" required>
                                                                <option value="0" <?= $result['department_id'] === 0 ? 'selected' : '' ?>>All</option>
                                                                <?php 
                                                                $departments = $db->showDepartments_WHERE_ACTIVE();
                                                                foreach ($departments as $dept) {
                                                                    $selected = ($dept['id'] == $result['department_id']) ? 'selected' : '';
                                                                    echo "<option value='{$dept['id']}' {$selected}>{$dept['name']}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="row m-r-0 m-l-0 mt-3"> 
                                                            <div class="col-12 p-0"> 
                                                                <label class="info-label m-l-4">Permissions</label> 
                                                                <div class="permissions-list">
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="dashboard" 
                                                                            <?= in_array('dashboard', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>Dashboard Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="dashboard_view" 
                                                                                <?= in_array('dashboard_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View Dashboard</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="dashboard_download" 
                                                                                <?= in_array('dashboard_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div> 
                                                                    </div> 
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="research_paper" 
                                                                            <?= in_array('research_paper', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>Research Paper Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="research_view" 
                                                                                <?= in_array('research_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View Research Papers</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="research_add" 
                                                                                <?= in_array('research_add', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Add Research Papers</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="research_edit" 
                                                                                <?= in_array('research_edit', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Edit Research Papers</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="research_delete" 
                                                                                <?= in_array('research_delete', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Delete Research Papers</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="research_publish" 
                                                                                <?= in_array('research_publish', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Publish Research Papers</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="research_download" 
                                                                                <?= in_array('research_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div> 
                                                                    </div> 
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="student_list" 
                                                                            <?= in_array('student_list', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>Student List Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="student_list_view" 
                                                                                <?= in_array('student_list_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View Student</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="student_list_edit" 
                                                                                <?= in_array('student_list_edit', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Edit Student</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="student_list_delete" 
                                                                                <?= in_array('student_list_delete', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Delete Student</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="student_list_status" 
                                                                                <?= in_array('student_list_status', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Change Status</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="student_list_download" 
                                                                                <?= in_array('student_list_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div> 
                                                                    </div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row m-r-0 m-l-0 mt-3"> 
                                                            <div class="col-12 p-0"> 
                                                                <label class="info-label m-l-4">Other Permissions</label> 
                                                                <div class="permissions-list">
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="department" 
                                                                            <?= in_array('department', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>Department Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="department_view" 
                                                                                <?= in_array('department_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View Department</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="department_add" 
                                                                                <?= in_array('department_add', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Add Department</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="department_edit" 
                                                                                <?= in_array('department_edit', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Edit Department</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="department_delete" 
                                                                                <?= in_array('department_delete', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Delete Department</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="department_status" 
                                                                                <?= in_array('department_status', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Change Status</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="department_download" 
                                                                                <?= in_array('department_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="course" 
                                                                            <?= in_array('course', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>Course Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="course_view" 
                                                                                <?= in_array('course_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View Course</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="course_add" 
                                                                                <?= in_array('course_add', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Add Course</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="course_edit" 
                                                                                <?= in_array('course_edit', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Edit Course</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="course_delete" 
                                                                                <?= in_array('course_delete', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Delete Course</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="course_status" 
                                                                                <?= in_array('course_status', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Change Status</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="course_download" 
                                                                                <?= in_array('course_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="role" 
                                                                            <?= in_array('role', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>Role Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="role_view" 
                                                                                <?= in_array('role_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View Role</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="role_add" 
                                                                                <?= in_array('role_add', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Add Role</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="role_edit" 
                                                                                <?= in_array('role_edit', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Edit Role</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="role_delete" 
                                                                                <?= in_array('role_delete', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Delete Role</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="role_status" 
                                                                                <?= in_array('role_status', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Change Status</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="role_download" 
                                                                                <?= in_array('role_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="permission-group">
                                                                        <div class="permission-group-header">
                                                                            <input type="checkbox" name="permissions[]" value="user" 
                                                                            <?= in_array('user', $existingPermissions) ? 'checked' : '' ?>>
                                                                            <h6>User Access</h6>
                                                                        </div>
                                                                        <div class="permission-group-items">
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_view" 
                                                                                <?= in_array('user_view', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>View User</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_add" 
                                                                                <?= in_array('user_add', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Add User</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_edit" 
                                                                                <?= in_array('user_edit', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Edit User</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_delete" 
                                                                                <?= in_array('user_delete', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Delete User</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_logs" 
                                                                                <?= in_array('user_logs', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>User Logs</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_status" 
                                                                                <?= in_array('user_status', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Change Status</label>
                                                                            </div>
                                                                            <div class="permission-item">
                                                                                <input type="checkbox" name="permissions[]" value="user_download" 
                                                                                <?= in_array('user_download', $existingPermissions) ? 'checked' : '' ?>>
                                                                                <label>Download PDF</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="edit_role" name="edit_role" class="btn btn-danger">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                endif;
                                ?>
                            </tr>
                            <?php
                            }
                        }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php include 'templates/footer.php'; ?>
    </div>
<?php endif; ?>
<script>
function initializeEditPermissions(roleId, existingPermissions) {
    const permissionArray = existingPermissions.split(',');
    
    // Check the appropriate boxes based on existing permissions
    permissionArray.forEach(permission => {
        const checkbox = document.querySelector(`#editRoleModal_${roleId} input[value="${permission}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
    
    // Initialize the same permission logic as in add mode
    // (Copy the permission checkbox logic from your add form)
    
}
document.addEventListener('DOMContentLoaded', function() {
function handlePermissionGroup(modalSelector,mainCheckboxSelector, permissionCheckboxSelector, viewPermissionValue) {
    const modal = document.querySelector(modalSelector);
    if (!modal) return;
    const mainCheckbox = modal.querySelector(mainCheckboxSelector);
    const permissions = modal.querySelectorAll(permissionCheckboxSelector);

    mainCheckbox.addEventListener('change', function () {
        permissions.forEach(checkbox => {
            checkbox.checked = this.checked;
            checkbox.disabled = false;
        });
    });

    permissions.forEach(checkbox => {
        if (checkbox.value !== mainCheckbox.value) {
            checkbox.addEventListener('change', function () {
                if (this.value === viewPermissionValue && !this.checked) {
                    permissions.forEach(cb => {
                        if (cb.value !== viewPermissionValue && cb.value !== mainCheckbox.value) {
                            cb.checked = false;
                            cb.disabled = true;
                        }
                    });
                    mainCheckbox.checked = false;
                }

                if (this.value === viewPermissionValue && this.checked) {
                    permissions.forEach(cb => {
                        if (cb.value !== viewPermissionValue && cb.value !== mainCheckbox.value) {
                            cb.disabled = false;
                        }
                    });
                }

                const allChecked = Array.from(permissions)
                    .filter(cb => cb.value !== mainCheckbox.value)
                    .every(cb => cb.checked);
                mainCheckbox.checked = allChecked;
            });
        }
    });
}

// Initialize permission groups
handlePermissionGroup('#addRoleModal', 'input[value="dashboard"]', 'input[value^="dashboard_"]', 'dashboard_view');
handlePermissionGroup('#addRoleModal', 'input[value="research_paper"]', 'input[value^="research_"]', 'research_view');
handlePermissionGroup('#addRoleModal', 'input[value="student_list"]', 'input[value^="student_list_"]', 'student_list_view');
handlePermissionGroup('#addRoleModal', 'input[value="department"]', 'input[value^="department_"]', 'department_view');
handlePermissionGroup('#addRoleModal', 'input[value="course"]', 'input[value^="course_"]', 'course_view');
handlePermissionGroup('#addRoleModal', 'input[value="role"]', 'input[value^="role_"]', 'role_view');
handlePermissionGroup('#addRoleModal', 'input[value="user"]', 'input[value^="user_"]', 'user_view');

document.querySelectorAll('.modal[id^="editRoleModal_"]').forEach(modal => {
    const modalId = `#${modal.id}`;
    handlePermissionGroup(modalId, 'input[value="dashboard"]', 'input[value^="dashboard_"]', 'dashboard_view');
    handlePermissionGroup(modalId, 'input[value="research_paper"]', 'input[value^="research_"]', 'research_view');
    handlePermissionGroup(modalId, 'input[value="student_list"]', 'input[value^="student_list_"]', 'student_list_view');
    handlePermissionGroup(modalId, 'input[value="department"]', 'input[value^="department_"]', 'department_view');
    handlePermissionGroup(modalId, 'input[value="course"]', 'input[value^="course_"]', 'course_view');
    handlePermissionGroup(modalId, 'input[value="role"]', 'input[value^="role_"]', 'role_view');
    handlePermissionGroup(modalId, 'input[value="user"]', 'input[value^="user_"]', 'user_view');

});
});


const departmentSelect = document.getElementById('department');
departmentSelect.addEventListener('change', function() {
    const selectedDepartment = this.value;
    const isAllDepartments = selectedDepartment === 'all';
    
    // Update help text based on selection
    const permissionsHelpText = document.querySelector('.permissions-help-text');
    if (permissionsHelpText) {
        if (isAllDepartments) {
            permissionsHelpText.textContent = 'Permissions will apply to all departments';
        } else {
            const departmentName = this.options[this.selectedIndex].text;
            permissionsHelpText.textContent = `Dashboard, Research, Student List permissions will only apply to ${departmentName}`;
        }
    }
});
// Get all research paper permission checkboxes
const permissionHeaders = document.querySelectorAll('.permission-group-header');

permissionHeaders.forEach(header => {
    header.addEventListener('click', function(e) {
        if (e.target.type === 'checkbox') return;
        
        const permissionGroup = this.closest('.permission-group');
        permissionGroup.classList.toggle('active');
    });
});


new DataTable('#datatablesss');

$('#datatablesss_filter label input').removeClass('form-control form-control-sm');
$('#datatablesss_wrapper').children('.row').eq(1).find('.col-sm-12').css({
    'padding-left': 0,
    'padding-right': 0
});
function confirmDelete(roleID){
    swal({
        title: "Are you sure you want to delete?",
        text: "You will not be able to recover this data!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#a33333",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if (isConfirm) {
            $.ajax({
                url: "delete_role.php",
                type: "GET",
                data: { roleID: roleID },
                success: function(response) {
                    swal({
                        title: "Deleted!",
                        text: "Role deleted.",
                        type: "success",
                        confirmButtonText: 'Okay',
                    }, 
                    function (isConfirm) {
                        location.reload();
                    });
                }
            });
        }
    });
}

$(document).ready(function(){
  $("#inputDepartment").change(function(){
    var department = $(this).val();
    //do something with secondIdValue

if(department != " "){

// $("#studentsection").show();

    // alert(course);
          $.ajax({
            url:"show_course.php",
            method:"POST",
            data:{"send_department_set":1, "send_department":department},

            success:function(data){
              $("#course").html(data);
              $("#course").css("display","block");
            }
          });
        }else{
          $("#course").css("display","none");
        }

});

$("#add_email").on("input", function() {
        validateEmailAddress('add_email', 'add-email-error');
    });

$('[id^="edit_email"]').each(function() {
    const id = $(this).attr('id');
    const errorId = id.replace('edit_email', 'edit-email-error');
    $(this).on("input", function() {
        validateEmailAddress(id, errorId);
    });
});

    function validateEmailAddress(inputId, errorId) {
        const emailInput = document.getElementById(inputId);
        const errorElement = document.getElementById(errorId);
        const emailValue = emailInput.value;

        errorElement.textContent = "";

        fetch("../php/checkAdminEmailAddress.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `email=${encodeURIComponent(emailValue)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists === true) {
                $(`#${inputId}`).css('background-image', 'url("../images/close.png")');
                if (data.message !== null) {
                    errorElement.textContent = "Email address already in use.";
                }
            } else {
                $(`#${inputId}`).css('background-image', 'url("../images/checked.png")');
                errorElement.textContent = "";
            }
        })
        .catch(error => console.error("Error:", error));
    }


$('.toggle-status').change(function() {
        var roleID = $(this).data('id');
        var status = $(this).prop('checked') ? 'Active' : 'Inactive';

        $.ajax({
            url: 'update_role_status.php',
            type: 'POST',
            data: {
                roleID: roleID,
                status: status
            },
            success: function(response) {
                // response = JSON.parse(response);

                // console.log(response);
                // sweetAlert(response.alert, response.status, response.statusCode);
            },
            error: function() {
                alert('Error updating role status.');
            }
        });
    });
  });


</script>

<?php 
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {

?>
    <script>
    sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
    </script>
<?php
unset($_SESSION['status']);
}
?>


</body>

</html>