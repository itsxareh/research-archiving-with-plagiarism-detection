<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='login.php'</script>";
    
}

if(ISSET($_POST['add_course'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

    $department = $_POST['department'];
    $course_name = $_POST['course_name'];
    

        $sql = $db->insert_Course($department, $course_name);

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully inserted a Department.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Department Added Successfully";
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
                <div class="row">
                    <div class="col-lg-8 p-r-0 title-margin-right">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Course List</h1>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                    <div class="col-lg-4 p-l-0 title-margin-left">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Course List</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                </div>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary " style="float: right;" data-toggle="modal" data-target="#modelId">
                <i class="ti-plus"></i> Add New Course
                </button>
                
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
<br><br><br>
                <table id="datatablesss" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Department Name</th>
                <th>Course Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            $data = $db->SELECT_ALL_COURSES();

            foreach ($data as $result) {
            ?>
            <tr>
                <td><?= $result['name'] ?></td>
                <td><?= $result['course_name'] ?></td>
                <td>
                    <?php 
                        $status = $result['course_status'];
                        $badgeColor = ($status === 'Active') ? 'badge-success' : 'badge-danger';
                    ?>
                    <span class="badge <?= $badgeColor ?>">
                        <?= $status ?>
                    </span>
                </td>

                <td style="text-align: left;">
                    <a href="course_status.php?courseID_activate=<?= $result['course_ID'] ?>" class="btn btn-success"><i class="ti-check" title="Publish Now"></i></a>
                    <a href="course_status.php?courseID=<?= $result['course_ID'] ?>" class="btn btn-danger"><i class="ti-close" title="Unpublish Now"></i></a>
                    

                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Department Name</th>
                <th>Course Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>


                <!-- /# row -->
                <section id="main-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="extra-area-chart"></div>
                            <div id="morris-line-chart"></div>
                            <div class="footer">
                                <p>2024 © Admin Board. -
                                    <a href="#">example.com</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


<script>
    new DataTable('#datatablesss');
</script>

<script>

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