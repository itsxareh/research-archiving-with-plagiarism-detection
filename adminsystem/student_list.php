<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
$departmentId = $userRole['department_id'];
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
    
} elseif(!hasPermit($permissions, 'student_list_view')) {
    header('Location:../../bad-request.php');
    exit(); 
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $filterByDepartment = ($departmentId != 0);
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
    <title>Student List: EARIST Repository</title>
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
     
    <link rel="stylesheet" href="../css/login-sign-up.css">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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


<?php if (hasPermission($permissions, 'student_list_view')): ?>
    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="col-sm-12 col-md-12 col-xl-12 title-page">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12  flex justify-content-between align-items-center page-title">
                                <h1 style="display: flex; ">Students</h1>
                                <?php if (hasPermission($permissions, 'student_list_download')): ?>

                                <div class="generate-report ">
                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_students" class="btn print-button">
                                        Print
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="col-md-12 list-container">
            <table id="datatablesss" class="table list-table" style="width:100%">
            <thead>
                <tr>
                    <th class="list-th">Student Number</th>    
                    <th class="list-th">Full Name</th>
                    <th class="list-th">Email</th>
                    <th class="list-th">No. of Research Papers</th>
                    <th class="list-th">Published Research</th>
                    <th class="list-th">Status</th>
                    <th class="list-th"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($departmentId != 0) {
                    $data = $db->SELECT_ALL_StudentsData_WHERE_DEPARTMENT_IS($departmentId);
                } else {
                    $data = $db->SELECT_ALL_StudentsData();
                }

                foreach ($data as $result) {
                    $studentId = htmlspecialchars($result['student_id']);
                    $uniquePrefix = "student_" . $studentId . "_";
                ?>
                <tr>
                    <td class="list-td"><?= $result['student_id'] ?></td>
                    <td class="list-td"><?= $result['first_name'] ?> <?= $result['middle_name'] ?> <?= $result['last_name'] ?></td>
                    <td class="list-td"><?= $result['student_email'] ?></td>
                    <td class="list-td" style="font-weight: 700;"><?=  $result['total_research'] ?></td>
                    <td class="list-td" style="font-weight: 700;"><?= $result['published_research'] ?></td>
                    <td class="list-td">
                        <?php
                            $status = $result ['school_verify'];
                            $badgeColor = ($status ==='Approved') ? 'badge-success' : 'badge-danger';
                        ?>
                        <span class= "badge <?= $badgeColor ?>" style="border-radius: 15px; font-size: 0.875rem">
                            <?= $status ?>
                        </span>

                    <td class="list-td" style="text-align:center">
                        <div class="action-container">
                            <div>
                                <button type="button" class="action-button"  id="action-button_<?= $result['student_id'] ?>" aria-expanded="true" aria-haspopup="true">
                                    Action
                                    <svg class="action-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="dropdown-action" id="dropdown_<?= $result['student_id'] ?>" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                <div role="none">
                                    <?php if (hasPermission($permissions, 'student_list_view')): ?>
                                    <a href="view_profile.php?studID=<?= $result['student_id'] ?>" class="dropdown-action-item">View Information</a>
                                    <?php endif; ?>
                                    <?php
                                    if (hasPermission($permissions, 'student_list_status')):
                                        $school_status = $result['school_verify'];
                                        if ($school_status === 'Approved'){
                                            echo '<a href="block_student.php?studID='.$result['studID'].'" class="dropdown-action-item">Block Student</a>';
                                        } elseif ($school_status === 'Not Approved'){
                                             echo '<a href="approval_student.php?studID='.$result['studID'].'" class="dropdown-action-item">Approve Student</a>';
                                        } elseif ($school_status === 'Blocked'){
                                            echo '<a href="unblock_student.php?studID='.$result['studID'].'" class="dropdown-action-item">Unblock Student</a>';
                                        }
                                    endif;
                                    ?>
                                    <?php if (hasPermission($permissions, 'student_list_edit')): ?>
                                    <a href="#" data-toggle="modal"  data-target="#<?= $uniquePrefix ?>edit_modal" class="dropdown-action-item">Edit information</a>
                                    <?php endif; ?>
                                    <?php if (hasPermission($permissions, 'student_list_delete')): ?>
                                    <a onclick="confirmDelete(<?= $result['studID'] ?>)" href="#" class="dropdown-action-item">Delete Student</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                
                <?php 
                    if (hasPermission($permissions, 'student_list_edit')):
                        include 'templates/edit_student_modal.php'; 
                    endif;
                ?>
                <?php
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
    new DataTable('#datatablesss');
    
$('#datatablesss_filter label input').removeClass('form-control form-control-sm');
</script>

<script>
    
function confirmDelete(studID){
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
                url: "delete_student.php",
                type: "GET",
                data: { studID: studID },
                success: function(response) {
                    swal({
                        title: "Deleted!",
                        text: "Student deleted.",
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
</script>
</body>

</html>