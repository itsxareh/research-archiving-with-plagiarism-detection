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

    <title>Student Info: EARIST Research Archiving System</title>

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

    <!-- Common -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="../student/css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



<div class="content-wrap">
    <div class="container">
      <div class=" col-md-12">
        <div class="row">
          <div class="col-lg-12">
            <div class="page-header">
            </div>
          </div>
        </div>
        <section id="main-content">
          <!-- Begin Page Content -->
          <div class="container-fluid">

<!-- Page Heading -->
<div class="profile-section">
  <?php
if(isset($_GET['studID'])){

  $student_id = $_GET['studID'];

  $data = $db->view_profile($student_id);

  if (isset($data) && is_array($data)) {
    $first_name = $data['first_name'] ?? 'Unknown';
    $last_name = $data['last_name'] ?? 'Unknown';
    $student_id = $data['studID'] ?? 'N/A';
    $course_name = $data['course_name'] ?? 'N/A';
    $department_name = $data['department_name'] ?? 'N/A';
    $email = $data['student_email'] ?? 'No email';
    $phone_number = $data['phone_number'] ?? 'No phone number';
    $profile_picture = $data['profile_picture'] ?? '';
    $verify_status = $data['verify_status'] ?? 'Not verified';
?>

  <div class="row col-md-12" style="height: auto;">
    <div class="col-md-8 p-l-0">
    <div class="profile-container">
      <p class="text-24 text-black"><?= $data['first_name'].' '.$data['last_name']?></p>
      <hr>
        <div class="info-details">
          <div class="item-detail">
              <span class="info-label">Student no.</span>
              <span class="profile-info"><?php echo $data['studID']; ?></span>
          </div>
          <div class="item-detail">
              <span class="info-label">Course</span>
              <span class="profile-info"><?php echo $data['course_name']; ?></span>
          </div>
          <div class="item-detail">
              <span class="info-label">Department</span>
              <span class="profile-info"><?php echo $data['name']; ?></span>
          </div>
      </div>
      <p class="profile-title">Contact</p>
      <hr>
        <div class="info-details">
          <div class="item-detail">
              <span class="info-label">Email address</span>
              <span class="profile-info"><?php echo $data['student_email']; ?></span>
          </div>
          <div class="item-detail">
              <span class="info-label">Phone number</span>
              <span class="profile-info"><?php echo '0'.$data['phone_number']; ?></span>
          </div>
      </div>
      <p class="profile-title">Account</p>
      <hr>
        <div class="info-details">
          <div class="item-detail">
              <span class="info-label">Status</span>
              <span class="profile-info"><?php echo $data['verify_status']; ?></span>
          </div>
      </div>
    </div>
  </div>
  <?php
    } else {
        echo '<p>No profile data found.</p>';
    }
  }
  ?>
  <div class="col-md-4 p-r-0">
    <div class="content">
      <div class="profile-img">
        <img id="viewImage" src="<?php echo $data['profile_picture'];?>" alt="Profile Image">
      </div>
    </div>
  </div>
  </div>
  </div>
  
  <div class="profile-row col-md-12">
    <p class="profile-title">Works</p>
    <hr>
    <div class="work-details">
    <?php
      if (!empty($data['works'])) {
          $i = 1;
          foreach ($data['works'] as $work) {
            echo '<a href="view_archive_research.php?archiveID=' . $work['aid'] . '" class="info-label"><span style="display: flex;">'.'['.$i.'] '.'<i class="project-work">'.ucwords($work['project_title']) . '</i></span></a><br>';
            $i += 1;
          }
      } else {
          echo 'No works available';
      }
      ?>
  </div>
  </div>
  </div>
</div>
</div>
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