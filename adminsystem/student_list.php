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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- theme meta -->
    <meta name="theme-name" content="focus" />
    <title>Student List: EARIST Research Archiving System</title>
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
                <div class="col-md-12 p-r-0 title-page">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Students</h1>
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
                    <th class="list-th">Status</th>
                    <th class="list-th">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $data = $db->SELECT_ALL_StudentsData();

                foreach ($data as $result) {
                ?>
                <tr>
                    <td class="list-td"><?= $result['student_id'] ?></td>
                    <td class="list-td"><?= $result['first_name'] ?> <?= $result['middle_name'] ?> <?= $result['last_name'] ?></td>
                    <td class="list-td"><?= $result['student_email'] ?></td>
                    <td class="list-td">
                        <?php
                            $status = $result ['school_verify'];
                            $badgeColor = ($status ==='Approved') ? 'badge-success' : 'badge-danger';
                            ?>
                            <span class= "badge <?= $badgeColor ?>">
                            <?= $status ?>
                </span>
                    <td class="list-td">
                        <a href="view_profile.php?studID=<?= $result['student_id'] ?>" class="btn btn-primary"><i class="ti-eye" title="View Information"></i></a>
                        <a href="approval_student.php?studID=<?= $result['studID'] ?>" class="btn btn-success"><i class="ti-check" title="Approve Student"></i></a>
                        <a href="delete_student.php?studID=<?= $result['studID'] ?>" class="btn btn-danger"><i class="ti-trash" title="Delete Student"></i></a>
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
  });


</script>



    <!-- Common -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>


    <!-- Sweet Alert -->
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>


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