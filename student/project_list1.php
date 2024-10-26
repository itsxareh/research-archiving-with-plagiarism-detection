<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['student_id']==0){
    echo"<script>window.location.href='login.php'</script>";
    
}


if(ISSET($_POST['add_research'])){

    $student_id = $_SESSION['auth_user']['student_id'];

    $project_title = $_POST['project_title'];
    $year = $_POST['year'];
    $department = $_SESSION['auth_user']['department_id'];
    $department_course = $_SESSION['auth_user']['course_id'];
    $abstract = $_POST['abstract'];
    $project_members = $_POST['project_members'];
    $owner_email = $_SESSION['auth_user']['student_email'];

    $randomNumber = rand(1000000000, 9999999999);

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');

    $uploadDirectoryFILES = '../pdf_files/'; 

    $uniqueFilenamePDF = uniqid() . '-' . $_FILES['project_file']['name'];


    $pdfPath = $uploadDirectoryFILES . $uniqueFilenamePDF;

    if (move_uploaded_file($_FILES['project_file']['tmp_name'], $pdfPath)) {

        $sql = $db->insert_Archive_Research($randomNumber, $department, $department_course, $project_title, $date, $year, $abstract, $owner_email, $project_members, $pdfPath);
        
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully submitted your Research Paper';

        $sql1 = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Research Added Successfully";
        $_SESSION['status-code'] = "success";
    } else {
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Failed to move image file.";
        $_SESSION['status-code'] = "error";
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
    <title>Project List: EARIST Research Archiving System</title>
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
require_once 'templates/student_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-r-0 title-margin-right">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Research Project List</h1>
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
                                    <li class="breadcrumb-item active">Research Project List</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                </div>

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary " style="float: right;" data-toggle="modal" data-target="#modelId">
                <i class="ti-plus"></i> Add New Research
                </button>
                
                <!-- Modal -->
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Research</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">

                                  <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Project Title</label>
                                    <input type="text" class="form-control" name="project_title" placeholder="Enter Research Title...">
                                    </div>

                                    <div class="col-sm-6">
                                    <label for="">Enter a year</label>
                                    <input type="number" class="form-control" name="year" min="1900" max="2100" placeholder="Ex: 2023">
                                    </div>
                                  </div>
                                  
                                  
                                  <label for="">Abstract</label>
                                  <textarea class="form-control" name="abstract" placeholder="The Research Abstract..."></textarea>
                                  
                                  <label for="">Project Members</label>
                                  <input type="text" class="form-control" name="project_members" placeholder="Ex: John Doe, Peter Parker, Tony Stark">
                                  
                                  <label for="">Project File (PDF)</label>
                                  <input type="file" accept=".pdf" class="form-control" name="project_file" >
                                  <p style="color: #FFFFFF; text-align: justify; font-size: 15px; margin-top: 8px;">
                                  <strong style= "color: #FF0000;">Note:</strong> Please ensure that the PDF you upload is in IMRAD format
                                   </p>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button name="add_research" class="btn btn-primary">Save</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
<br><br><br>
                <table id="datatablesss" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Date Created</th>
                <th>Archive Code</th>
                <th>Research Title</th>
                <th>Course</th>
                <th>Date Published</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $student_email = $_SESSION['auth_user']['student_email'];
            
            $data = $db->SELECT_ALL_STUDENT_ARCHIVE_RESEARCH($student_email);

            foreach ($data as $result) {
            ?>
            <tr>
                <td><?= $result['dateOFSubmit'] ?></td>
                <td><?= $result['archive_id'] ?></td>
                <td><?= $result['project_title'] ?></td>
                <td><?= $result['course_name'] ?></td>
                <td><?= $result['date_published'] ?></td>
                <td>
                    <?php 
                        $status = $result['document_status'];
                        $badgeColor = ($status === 'Accepted') ? 'badge-success' : 'badge-danger';
                    ?>
                    <span class="badge <?= $badgeColor ?>">
                        <?= $status ?>
                    </span>
                </td>

                <td>
                    <a href="view_project_research.php?archiveID=<?= $result['archive_id'] ?>" class="btn btn-primary"><i class="ti-eye" title="View Research"></i></a>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Date Created</th>
                <th>Archive Code</th>
                <th>Research Title</th>
                <th>Course</th>
                <th>Date Published</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </tfoot>
    </table>


                <!-- /# row -->
                <section id="main-content">
                    <div class="row">
                        <div class="col-lg-12" style="margin-top: 400px;">
                            <div id="extra-area-chart"></div>
                            <div id="morris-line-chart"></div>
                            <div class="footer">
                                <p>2024 © BSIT Students -
                                    <a href="#">earistsrams.com</a>
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