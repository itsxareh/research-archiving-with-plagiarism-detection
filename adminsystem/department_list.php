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
    
} elseif(!hasPermit($permissions, 'department_view')) {
    header('Location:../../bad-request.php');
    exit(); 
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}



if(ISSET($_POST['add_department'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

    $department_code = $_POST['department_code'];

    $department_name = $_POST['department_name'];
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    

        $sql = $db->insert_Department($department_code, $department_name, $description);

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You added a new department.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "New department added";
        $_SESSION['status-code'] = "success";
    
}





if(ISSET($_POST['edit'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];
    
    $dept_id = $_POST['dept_id'];
    $department_code = $_POST['dept_code'];

    $department_name = $_POST['dept_name'];
    $description = '';
    

        $sql = $db->update_Department($department_code, $department_name, $description, $dept_id);

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully updated a department.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Department Updated Successfully";
        $_SESSION['status-code'] = "success";
    
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
    <title>Department List: EARIST Repository</title>
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
    <link href="../css/styles.css" rel="stylesheet">
    <link href="../css/action-dropdown.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">


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


<?php if (hasPermission($permissions, 'department_view')): ?>
    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="col-sm-12 col-md-12 col-xl-12 title-page">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12  flex justify-content-between align-items-center page-title">
                                <h1 style="display: flex; ">Departments</h1>
                                <?php if (hasPermission($permissions, 'department_download')): ?>
                                <div class="generate-report ">
                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_departments" class="btn print-button">
                                        Print
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <?php if (hasPermission($permissions, 'department_add')): ?>
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add department</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="item-detail">
                                            <label for="" class="info-label m-l-4">Acronym</label>
                                            <input type="text" class="info-input" name="department_code" required>
                                        </div>
                                        <div class="item-detail">
                                            <label for="" class="info-label m-l-4">Department</label>
                                            <input type="text" class="info-input" name="department_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button name="add_department" class="btn btn-danger">Save</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-md-12 ">
                    <!-- Button trigger modal -->
                    <?php if (hasPermission($permissions, 'department_add')): ?>
                    <div class="add-department">
                        <button type="button" class="add-department-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add department
                        </button>
                    </div>
                    <?php endif; ?>
                <div class="list-container">
                    <table id="datatablesss" class="table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="list-th">Department Code</th>
                                <th class="list-th">Department</th>
                                <!-- <th>Description</th> -->
                                <th class="list-th">Status</th>
                                <th class="list-th"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            $data = $db->showDepartments();

                            foreach ($data as $result) {
                            ?>
                            <tr>
                                <td class="list-td"><?= $result['dept_code'] ?></td>
                                <td class="list-td"><?= $result['name'] ?></td>
                                <!-- <td><?= substr($result['description'], 0, 60) ?>...</td> -->
                                <td class="list-td" style="text-align: center;">
                                    <!-- <?php 
                                        $status = $result['department_status'];
                                        $badgeColor = ($status === 'Active') ? 'badge-success' : 'badge-danger';
                                    ?>
                                    <span class="badge <?= $badgeColor ?>">
                                        <?= $status ?>
                                    </span> -->
                                    <label class="switch">
                                    <input 
                                        type="checkbox" 
                                        class="toggle-status" 
                                        data-id="<?= $result['id'] ?>" 
                                        data-toggle="toggle" 
                                        data-on="Accept" 
                                        data-off="Don't Accept" 
                                        data-onstyle="success" 
                                        data-offstyle="danger"
                                        <?= ($result['department_status'] === 'Active') ? 'checked' : '' ?>
                                        <?php if (!hasPermission($permissions, 'department_status')): ?>
                                            disabled
                                        <?php endif; ?>
                                    >
                                    <span class="slider round"></span>
                                    </label>
                                </td>

                                <td class="list-td" style="text-align: center;">
                                    <div class="action-container">
                                        <div>
                                            <button type="button" class="action-button"  id="action-button_<?= $result['id'] ?>" aria-expanded="true" aria-haspopup="true">
                                                Action
                                                <svg class="action-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="dropdown-action" id="dropdown_<?= $result['id'] ?>" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                            <div role="none">
                                                <?php if (hasPermission($permissions, 'department_edit')): ?>   
                                                    <a href="#" data-toggle="modal" data-target="#modelId_<?= $result['id'] ?>" class="dropdown-action-item">Edit department</a>
                                                <?php endif; ?>
                                                <?php if (hasPermission($permissions, 'department_delete')): ?>
                                                    <a onclick="confirmDelete(<?= $result['id'] ?>)" href="#" data-toggle="delete-modal" data-target="#delete_modelId_<?= $result['id'] ?>" class="dropdown-action-item">Delete department</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Modal -->
                                <?php if (hasPermission($permissions, 'department_edit')): ?>
                                <div class="modal fade" id="modelId_<?= $result['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog modal-md" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit department</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <form action="" method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" class="info-input" name="dept_id" value="<?= $result['id'] ?>" readonly>
                                                    <div class="form-group">
                                                        <div class="item-detail">
                                                            <label for="" class="info-label m-l-4">Acronym</label>
                                                            <input type="text" class="info-input" name="dept_code" value="<?= $result['dept_code'] ?>" required>
                                                        </div>
                                                        <div class="item-detail">
                                                            <label for="" class="info-label m-l-4">Department</label>
                                                            <input type="text" class="info-input" name="dept_name" value="<?= $result['name'] ?>" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button name="edit" class="btn btn-danger">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                </td>
                            </tr>
                            <?php
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
</script>

<script>
function confirmDelete(departmentID){
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
                url: "delete_department.php",
                type: "GET",
                data: { departmentID: departmentID },
                success: function(response) {
                    swal({
                        title: "Deleted!",
                        text: "Department deleted.",
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
$('#datatablesss_filter label input').removeClass('form-control form-control-sm');
$('#datatablesss_wrapper').children('.row').eq(1).find('.col-sm-12').css({
    'padding-left': 0,
    'padding-right': 0
});
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

$('.toggle-status').change(function() {
        var departmentID = $(this).data('id');
        var status = $(this).prop('checked') ? 'Active' : 'Not Active';

        $.ajax({
            url: 'update_department_status.php',
            type: 'POST',
            data: {
                departmentID: departmentID,
                status: status
            },
            success: function(response) {
            },
            error: function() {
                alert('Error updating document status.');
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