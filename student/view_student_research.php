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


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Archive Research:  EARIST Repository</title>

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

    <!-- Common -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
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
                <h1>Research
                </h1>
              </div>
            </div>
          </div>
          <!-- /# column -->
          <div class="col-lg-4 p-l-0 ">
            <div class="page-header">
              <div class="page-title">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item">
                    <a href="dashboard.php">Dashboard</a>
                  </li>
                  <li class="breadcrumb-item active">Research</li>
                </ol>
              </div>
            </div>
          </div>
          <!-- /# column -->
        </div>
        <!-- /# row -->
        <section id="main-content">
          <!-- Begin Page Content -->
          <div class="container-fluid">

<!-- Page Heading -->

<div class="container bootstrap snippets bootdey">


<div class="panel-body inf-content">
<div class="row">
  <?php
if(isset($_GET['archiveID'])){

  $archiveID = $_GET['archiveID'];

  $data = $db->SELECT_ARCHIVE_RESEARCH($archiveID);
    
}
?>

</div>
<div class="col-md-6">
<strong>Information</strong><br>
<div class="table-responsive">
<table class="table table-user-information">
<tbody>

<tr>    
    <td>
        <strong>
            <span class="ti-id-badge"></span>    
            Archive ID                                               
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['archive_id']; ?> 
    </td>
</tr>

<tr>    
    <td>
        <strong>
            <span class="ti-book"></span>    
            Project Title                                              
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['project_title']; ?> 
    </td>
</tr>

<tr>        
    <td>
        <strong>
            <span class="ti-calendar"></span> 
            Project Year                                                
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['project_year']; ?>
    </td>
</tr>

<tr>        
    <td>
        <strong>
            <span class="ti-calendar"></span> 
            Date Published                                                
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['date_published']; ?>
    </td>
</tr>

<tr>        
    <td>
        <strong>
            <span class="ti-files"></span> 
            Document Status                                                
        </strong>
    </td>
    <td class="text-primary">
    <?php 
      $status = $data['document_status'];
      $badgeColor = ($status === 'Accepted') ? 'badge-success' : 'badge-danger';
    ?>
    <span class="badge <?= $badgeColor ?>">
    <?= $status ?>
    </span>
    </td>
</tr>

<tr>        
    <td>
        <strong>
            <span class="ti-user"></span> 
            Project Members                                                
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['project_members']; ?>
    </td>
</tr>


<tr>        
    <td>
        <strong>
            <span class="ti-email"></span> 
            Research Owner Email Address                                               
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['research_owner_email']; ?>
    </td>
</tr>

<tr>        
    <td>
        <strong>
            <span class="ti-calendar"></span> 
            Date of Upload                                                
        </strong>
    </td>
    <td class="text-primary">
    <?php echo $data['dateOFSubmit']; ?>
    </td>
</tr>

                                    
</tbody>
</table>
</div>
</div>
</div>

<div class="form-group">
    <label for="projectAbstract">Project Abstract</label>
    <textarea style="height: 200px;" class="form-control" id="projectAbstract" readonly><?php echo $data['project_abstract']; ?></textarea>
    <br>
    <label for="projectAbstract">Project File (PDF)</label>
    <iframe src="<?php echo $data['documents']; ?>" width="100%" height="400px" allowfullscreen></iframe>
</div>

</div>



</div>                                        

</div>
<!-- /.container-fluid -->
          <!-- /# row -->
          
          <div class="row">
            <div class="col-lg-12">
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



<!----------------UPLOAD OR UPDATE AN IMAGE AND DISPLAYS THE SELECTED IMAGE FIRST BEFORE UPDATING OR UPLOADING--------------->
<script>
    function previewImage(event) {
  var reader = new FileReader();
  reader.onload = function () {
    var output = document.getElementById('myImage');
    output.src = reader.result;
  }
  reader.readAsDataURL(event.target.files[0]);
}
</script>



    <!-- Common -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>

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