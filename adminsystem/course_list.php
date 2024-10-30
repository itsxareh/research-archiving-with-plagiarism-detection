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
        $logs = 'You successfully inserted a Course.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Course Added Successfully";
        $_SESSION['status-code'] = "success";
    
}




if(ISSET($_POST['edit'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    

        $sql = $db->update_Course($course_name, $course_id);

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully updated a Course Name.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Course Name Updated Successfully";
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
    <link rel="shortcut icon" href="images/logo1.png">
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
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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
                <div class="col-lg-12 p-r-0 title-page">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Courses</h1>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Course</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">

                                <div class="form-group">
                                      <label for="">Select Department</label>
                                      <select class="form-control" name="department">
                                        <option></option>

                                        <?php
                                        $data = $db->SELECT_ALL_DEPARTMENTS_WHERE_ACTIVE();
                                        
                                        foreach ($data as $result) {
                                        ?>
                                        <option value="<?=$result['id']?>"><?=$result['name']?></option>
                                        <?php
                                        }
                                        ?>

                                      </select>
                                  <br>
                                    <label for="">Course Name</label>
                                    <input type="text" class="form-control" name="course_name" >

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button name="add_course" class="btn btn-primary">Save</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- Button trigger modal -->
                    <div class="add-course">
                        <button type="button" class="add-course-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add New Course
                        </button>
                    </div>

                <div class="list-container">
                    <table id="datatablesss" class="table list-table" style="width:100%">
                        <thead>
                            <tr>
                                <th class="list-th">Course</th>
                                <th class="list-th">Department</th>
                                <th class="list-th">Status</th>
                                <th class="list-th">Action</th>
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

                                <td class="list-td" style="text-align: center;">
                                    <!-- <a href="course_status.php?courseID_activate=<?= $result['course_ID'] ?>" class="btn btn-success"><i class="ti-check" title="Publish Now"></i></a>
                                    <a href="course_status.php?courseID=<?= $result['course_ID'] ?>" class="btn btn-danger"><i class="ti-close" title="Unpublish Now"></i></a> -->
                                    <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#modelId_<?= $result['course_ID'] ?>">
                                <i class="ti-pencil"></i>
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modelId_<?= $result['course_ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">View/Edit Course</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                            </div>
                                            <form action="" method="post">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        
                                                        <label for="">Course Name</label>
                                                        <input type="hidden" class="form-control" name="course_id" value="<?= $result['course_ID'] ?>" readonly>
                                                        <input type="text" class="form-control" name="course_name" value="<?= $result['course_name'] ?>">

                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button name="edit" class="btn btn-success">Update</button>
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
</div>

<script>
    new DataTable('#datatablesss');
</script>

<script>
    $('#datatablesss_filter label input').removeClass('form-control form-control-sm');
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
        var status = $(this).prop('checked') ? 'Active' : 'Not Activated';

        $.ajax({
            url: 'update_course_status.php',
            type: 'POST',
            data: {
                courseID: courseID,
                status: status
            },
            success: function(response) {
                location.reload();
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