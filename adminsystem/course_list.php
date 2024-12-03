<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    
}


if(ISSET($_POST['add_course'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

    $department = $_POST['department'];
    $course_name = $_POST['course_name'];
    

        $sql = $db->insert_Course($department, $course_name);

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You added a new course.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);



        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "New course added";
        $_SESSION['status-code'] = "success";
    
}




if(ISSET($_POST['edit'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

    $department_id = $_POST['department'];
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    

        $sql = $db->update_Course($course_name, $department_id, $course_id);

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You updated the '.$course_name.' course.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Course updated";
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
    <title>Course List: EARIST Research Archiving System</title>
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



    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="col-sm-12 col-md-12 col-xl-12 title-page">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-xl-12  flex justify-content-between align-items-center page-title">
                                <h1 style="display: flex; ">Courses</h1>
                                <div class="generate-report ">
                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_courses" class="btn print-button">
                                        Print
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add course</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="item-detail">
                                            <label for="" class="info-label m-l-4">Department</label>
                                            <select class="info-input" name="department" id="department" required>
                                            <?php 
                                                $res = $db->showDepartments_WHERE_ACTIVE();

                                                foreach ($res as $item) {
                                                echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                                                }
                                            ?>
                                            </select>
                                        </div>
                                        <div class="item-detail">
                                            <label for="" class="info-label m-l-4">Course</label>
                                            <input type="text" class="info-input" id="course_name" name="course_name" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button name="add_course" class="btn btn-danger">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- Button trigger modal -->
                    <div class="add-course">
                        <button type="button" class="add-course-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add course
                        </button>
                    </div>

                <div class="list-container">
                    <table id="datatablesss" class="table list-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="list-th">Course</th>
                                <th class="list-th">Department</th>
                                <th class="list-th">Status</th>
                                <th class="list-th"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            $data = $db->SELECT_ALL_COURSES();       
                            foreach ($data as $result) {
                            ?>
                            <tr>
                                <td class="list-td"><?= $result['course_name'] ?></td>
                                <td class="list-td"><?= $result['name'] ?></td>
                                <td class="list-td" style="display:flex; align-items:center; justify-content:center; margin:auto">
                                    <!-- <?php 
                                        $status = $result['course_status'];
                                        $badgeColor = ($status === 'Active') ? 'badge-success' : 'badge-danger';
                                    ?>
                                    <span class="badge <?= $badgeColor ?>">
                                        <?= $status ?>
                                    </span> -->
                                    <label class="switch">
                                    <input 
                                        type="checkbox" 
                                        class="toggle-status" 
                                        data-id="<?= $result['course_ID'] ?>" 
                                        data-toggle="toggle" 
                                        data-on="Accept" 
                                        data-off="Don't Accept" 
                                        data-onstyle="success" 
                                        data-offstyle="danger"
                                        <?= ($result['course_status'] === 'Active') ? 'checked' : '' ?>
                                    >
                                    <span class="slider round"></span>
                                    </label>
                                </td>

                                <td class="list-td" style="text-align: start;">
                                    <div class="action-container">
                                        <div>
                                            <button type="button" class="action-button"  id="action-button_<?= $result['course_ID'] ?>" aria-expanded="true" aria-haspopup="true">
                                                Action
                                                <svg class="action-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="dropdown-action" id="dropdown_<?= $result['course_ID'] ?>" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                            <div role="none">
                                                <a href="#" data-toggle="modal" data-target="#modelId_<?= $result['course_ID'] ?>" class="dropdown-action-item">Edit course</a>
                                                <a onclick="confirmDelete(<?= $result['course_ID'] ?>)" href="#" data-toggle="delete-modal" data-target="#delete_modelId_<?= $result['course_ID'] ?>" class="dropdown-action-item">Delete course</a>
                                            </div>
                                        </div>
                                    </div>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modelId_<?= $result['course_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Course</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <form action="" method="post">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="item-detail">
                                                            <label for="" class="info-label m-l-4">Department</label>
                                                            <select class="info-input" name="department" id="department" required>
                                                            <?php 
                                                                $res = $db->showDepartments_WHERE_ACTIVE();

                                                                foreach ($res as $item) {
                                                                    if ($item['id'] == $result['department_id']) {
                                                                        echo "<option value='".$item['id']."' selected>".$item['name']."</option>";
                                                                    } else {
                                                                        echo "<option value='".$item['id']."'>".$item['name']."</option>";
                                                                    }
                                                                }
                                                            ?>
                                                            </select>
                                                        </div>
                                                        <div class="item-detail">
                                                            <label for="" class="info-label m-l-4">Course</label>
                                                            <input type="hidden" class="info-input" name="course_id" value="<?= $result['course_ID'] ?>" readonly>
                                                            <input type="text" class="info-input" id="course_name" name="course_name" value="<?= $result['course_name'] ?>" required>
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

<script>
new DataTable('#datatablesss');

$('#datatablesss_filter label input').removeClass('form-control form-control-sm');

function confirmDelete(courseID){
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
                url: "delete_course.php",
                type: "GET",
                data: { courseID: courseID },
                success: function(response) {
                    swal({
                        title: "Deleted!",
                        text: "Course deleted.",
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

    $('.toggle-status').change(function() {
        var courseID = $(this).data('id');
        var status = $(this).prop('checked') ? 'Active' : 'Not Active';

        $.ajax({
            url: 'update_course_status.php',
            type: 'POST',
            data: {
                courseID: courseID,
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