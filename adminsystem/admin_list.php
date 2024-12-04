<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    
} elseif(!hasPermit($permissions, 'user_view')) {
    header('Location:../../bad-request.php');
    exit(); 
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}



if(ISSET($_POST['add_admin'])){
    $uniqueID = uniqid();
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $complete_address = $_POST['complete_address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email_address'];
    $password = trim($_POST['password']);
    $role_id = $_POST['role_id'];

    $user = $db->admin_register_select_email($email, $admin_id);

    if ($user) {
        // Email already exists
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Email already exists";
        $_SESSION['status-code'] = "error";
        header("location: admin_list.php");
        exit();
    }

    $sql = $db->insert_Admin($uniqueID, $first_name, $last_name, $complete_address, $phone_number, $email, $password, $role_id);

    if ($sql){
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You added a new admin.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Added Successfully";
        $_SESSION['status-code'] = "success";
    }

}

if(ISSET($_POST['edit'])){
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $complete_address = $_POST['complete_address'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email_address'];
    $password = trim($_POST['password']);
    $role_id = $_POST['role_id'];
    
    $sql = $db->update_Admin($first_name, $last_name, $complete_address, $phone_number, $email, $password, $role_id, $id);

    if ($sql){
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You updated '.$first_name.' '.$last_name.' info.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Updated successfully";
        $_SESSION['status-code'] = "success";
    }
    
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
    <title>Admin List: EARIST Repository</title>
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
<?php if (hasPermission($permissions, 'user_view')): ?>
    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="col-lg-12 title-page">
                <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12  flex justify-content-between align-items-center page-title">
                                <h1 style="display: flex; ">Admins</h1>
                                <?php if (hasPermission($permissions, 'user_download')): ?>
                                <div class="generate-report ">
                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_admins" class="btn print-button">
                                        Print
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <?php if (hasPermission($permissions, 'user_add')): ?>
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add an admin</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row m-0" style="justify-content: space-between; row-gap: 6px">
                                            <div class="col-sm-6 item-detail p-0">
                                                <label for="" class="info-label m-l-4">First name</label>
                                                <input type="text" class="info-input" name="first_name" required>
                                            </div>
                                            <div class="col-sm-5 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Last name</label>
                                                <input type="text" class="info-input" name="last_name" required>
                                            </div>
                                        </div>
                                        <div class="row m-0" style="justify-content: space-between;">
                                            <div class="col-sm-6 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Email address</label>
                                                <input type="email" class="info-input" name="email_address" id="add_email" required>
                                                <span id="add-email-error" class="error-message" style="color: #a33333; font-size: 10px"></span>
                                            </div>
                                            <div class="col-sm-5 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Phone number</label>
                                                <input class="info-input" name="phone_number" oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" maxlength="11" required>
                                            </div>
                                        </div>
                                        <div class="row m-0" style="justify-content: space-between;">
                                            <div class="col-sm-6 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Password</label>
                                                <input type="password" class="info-input" name="password" required>
                                            </div>
                                            <div class="col-sm-5 item-detail p-0">
                                                <label for="" class="info-label m-l-4">Role</label>
                                                <select class="info-input" name="role_id" required>
                                                    <option value="" selected disabled>Select role</option>
                                                    <?php
                                                    $data = $db->showRoles();
                                                    foreach ($data as $role) {
                                                    ?>
                                                    <option value="<?= $role['roleID'] ?>"><?= $role['role_name'] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="item-detail">
                                            <label for="" class="info-label m-l-4">Complete Address</label>
                                            <textarea class="info-input" name="complete_address" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button name="add_admin" class="btn btn-danger">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-md-12 ">
                    <!-- Button trigger modal -->
                    <?php if (hasPermission($permissions, 'user_add')): ?>
                    <div class="add-department">
                        <button type="button" class="add-research-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add an admin
                        </button>
                    </div>
                    <?php endif; ?>

                <div class="list-container">
                    <table id="datatablesss" class="table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="list-th">Name</th>
                                <!-- <th>Description</th> -->
                                <th class="list-th">Email Address</th>
                                <th class="list-th">Phone number</th>
                                <th class="list-th">Role</th>
                                <th class="list-th">Status</th>
                                <th class="list-th"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            $data = $db->showAdmins($admin_id);
                            if (count($data) != 0) {
                            foreach ($data as $result) {
                            ?>
                            <tr>
                                <td class="list-td"><?= $result['first_name']. ' ' .$result['middle_name'].' ' .$result['last_name'] ?></td>
                                <td class="list-td"><?= $result['admin_email'] ?></td>
                                <td class="list-td"><?= $result['phone_number'] ?></td>
                                <td class="list-td"><?= $result['role_name'] ?></td>
                                <td class="list-td" style="text-align: center;">
                                    <!-- <?php 
                                        $status = $result['admin_status'];
                                        $badgeColor = ($status === 'Active') ? 'badge-success' : 'badge-danger';
                                    ?>
                                    <span class="badge <?= $badgeColor ?>">
                                        <?= $status ?>
                                    </span> -->
                                    <label class="switch">
                                    <input 
                                        type="checkbox" 
                                        class="toggle-status" 
                                        data-id="<?= $result['adminID'] ?>" 
                                        data-toggle="toggle" 
                                        data-on="Accept" 
                                        data-off="Don't Accept" 
                                        data-onstyle="success" 
                                        data-offstyle="danger"
                                        <?= ($result['admin_status'] === 'Active') ? 'checked' : '' ?>
                                        <?php if (!hasPermission($permissions, 'user_status')): ?>
                                            disabled
                                        <?php endif; ?>
                                        >
                                    <span class="slider round"></span>
                                    </label>
                                </td>

                                <td class="list-td" style="text-align: center;">
                                    <div class="action-container">
                                        <div>
                                            <button type="button" class="action-button"  id="action-button_<?= $result['adminID'] ?>" aria-expanded="true" aria-haspopup="true">
                                                Action
                                                <svg class="action-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="dropdown-action" id="dropdown_<?= $result['adminID'] ?>" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                            <div role="none">
                                                <?php if (hasPermission($permissions, 'user_edit')): ?>
                                                    <a href="#" data-toggle="modal" data-target="#modelId_<?= $result['adminID'] ?>" class="dropdown-action-item">Edit info</a>
                                                <?php endif; ?>
                                                <?php if (hasPermission($permissions, 'user_delete')): ?>
                                                    <a onclick="confirmDelete(<?= $result['adminID'] ?>)" href="#" data-toggle="delete-modal" data-target="#delete_modelId_<?= $result['adminID'] ?>" class="dropdown-action-item">Delete admin</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <?php if (hasPermission($permissions, 'user_edit')): ?>
                                <div class="modal fade" id="modelId_<?= $result['adminID'] ?>" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog modal-md" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Admin</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="" method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="text" style="display: none;" class="form-control" name="id" value="<?= $result['adminID'] ?>">
                                                    <div class="form-group">
                                                        <div class="row m-0" style="justify-content: space-between; row-gap: 6px">
                                                            <div class="col-sm-6 item-detail p-0">
                                                                <label for="" class="info-label m-l-4">First name</label>
                                                                <input type="text" class="info-input" name="first_name" value="<?= $result['first_name'] ?>" required>
                                                            </div>
                                                            <div class="col-sm-5 item-detail p-0">
                                                                <label for="" class="info-label m-l-4">Last name</label>
                                                                <input type="text" class="info-input" name="last_name" value="<?= $result['last_name'] ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="row m-0" style="justify-content: space-between;">
                                                            <div class="col-sm-6 item-detail p-0">
                                                                <label for="" class="info-label m-l-4">Email address</label>
                                                                <input type="email" class="info-input" name="email_address" id="edit_email<?= $result['adminID'] ?>" value="<?= $result['admin_email'] ?>" required>
                                                                <span id="edit-email-error<?= $result['adminID'] ?>" class="error-message" style="color: #a33333; font-size: 10px"></span>
                                                            </div>
                                                            <div class="col-sm-5 item-detail p-0">
                                                                <label for="" class="info-label m-l-4">Phone number</label>
                                                                <input class="info-input" name="phone_number" oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="[0-9]*" maxlength="11" value="<?= $result['phone_number'] ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="row m-0" style="justify-content: space-between;">
                                                            <div class="col-sm-6 item-detail p-0">
                                                                <label for="" class="info-label m-l-4">Password</label>
                                                                <input type="password" class="info-input" name="password" value="<?= $result['admin_password'] ?>" required>
                                                            </div>
                                                            <div class="col-sm-5 item-detail p-0">
                                                                <label for="" class="info-label m-l-4">Role</label>
                                                                <select class="info-input" name="role_id" required>
                                                                    <?php
                                                                    $data = $db->showRoles();
                                                                    foreach ($data as $role) {
                                                                    ?>
                                                                    <option value="<?= $role['roleID'] ?>" <?= ($role['roleID'] === $result['role_id']) ? 'selected' : '' ?>><?= $role['role_name'] ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="item-detail">
                                                        </div>
                                                        <div class="item-detail">
                                                            <label for="" class="info-label m-l-4">Complete Address</label>
                                                            <textarea class="info-input" name="complete_address" required><?= $result['complete_address'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" name="edit" class="btn btn-danger">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
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
    </div>
    <?php include 'templates/footer.php'; ?>
</div>
<?php endif; ?>

<script>
new DataTable('#datatablesss');

$('#datatablesss_filter label input').removeClass('form-control form-control-sm');
$('#datatablesss_wrapper').children('.row').eq(1).find('.col-sm-12').css({
    'padding-left': 0,
    'padding-right': 0
});
function confirmDelete(adminID){
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
                url: "delete_admin.php",
                type: "GET",
                data: { adminID: adminID },
                success: function(response) {
                    swal({
                        title: "Deleted!",
                        text: "Admin deleted.",
                        type: "success",
                        confirmButtonText: 'Okay',
                    }, 
                    function (isConfirm) {
                        // if (isConfirm) {
                        //     const listItem = document.getElementById(`li_${studID}`)
                        //     const searchResult = document.getElementById('search-result');
                        //     if (listItem){
                        //         listItem.remove();
                        //     }
                        //     if (!searchResult.querySelector('.project-list')) {
                        //         searchResult.innerHTML = "<p style='text-align: center'>No uploaded research found.</p>";
                        //     }
                        // }
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
        var adminID = $(this).data('id');
        var status = $(this).prop('checked') ? 'Active' : 'Not Active';

        $.ajax({
            url: 'update_admin_status.php',
            type: 'POST',
            data: {
                adminID: adminID,
                status: status
            },
            success: function(response) {
                // response = JSON.parse(response);

                // console.log(response);
                // sweetAlert(response.alert, response.status, response.statusCode);
            },
            error: function() {
                alert('Error updating admin status.');
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