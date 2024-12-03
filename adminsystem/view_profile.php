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

  $studID= $_GET['studID'];

  $data = $db->view_profile($studID);

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
        echo '<script>location.href = "../bad-request.php"</script>';
    }
  }
  ?>
  <div class="col-md-4 p-r-0">
    <div class="content">
      <div class="profile-img">
        <img id="viewImage" src="<?= isset($data['profile_picture']) ? $data['profile_picture'] : '../images/default-profile.svg' ?>" alt="Profile Image">
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
  <div class="">
    <p class="profile-title">Plagiarism History</p>
    <hr>
    <ul>
    <?php
      $plagiarism_history = $db->view_plagiarism_history($studID);
      if(!empty($plagiarism_history)){
        foreach($plagiarism_history as $history){
          $percentage = $history['plagiarism_percentage'];
          if ($percentage > 100){
              $percentage = 100;
          }
          echo '
          <li class="flex justify-content-between">
            <div class="" style="display: flex; justify-content: space-between; width: 100%;">
              <div class="" style="width: 59%;">
                    <p class="mb-0"><a href="plagiarism_result.php?archiveID='.$history['aid'].'" style="color: #333; font-size: 12px; font-weight:500">'.$history['project_title'].'</a></p>
                    <p class="" style=" font-size: 12px;">'.(new DateTime($history['dateOFSubmit']))->format("d F Y h:i:s A").'</p>
                </div>
                <div class="d-flex align-items-center" style="width: 39%;">
                    <div class="progress w-100 m-0" style="height: 10px">
                        <div class="progress-bar-danger progress-bar " role="progressbar" style="width: '.round($percentage, 1).'%" aria-valuenow="'.round($percentage, 1).'" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span style="color: #a33333; font-size: 16px; margin-left: .75rem !important;">'.round($percentage, 1).'%</span>
                </div>
            </div>
          </li>

          
          ';
        }
      } else {
        echo '<p class="text-center">No plagiarism history available.</p>';
      }
      ?>
    </ul>
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