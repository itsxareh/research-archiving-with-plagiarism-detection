<?php

include '../connection/config.php';
$db = new Database();
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
if($_SESSION['auth_user']['student_id']==0){
  $current_url = urlencode($_SERVER['REQUEST_URI']);
  header("Location: login.php?redirect_to=$current_url");
  exit();
}

if(isset($_GET['archiveID'])){

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');

      $archiveID = $_GET['archiveID'];
      $student_id = $_SESSION['auth_user']['student_id'];
    
    
      $data = $db->insert_Research_Views($archiveID, $student_id, $date);
        
    }

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Archive Research: EARIST Research Archiving System</title>

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
  <div class="container">
    <div class=" col-md-12">
      <div class="row">
        <div class="col-md-12 p-r-0 title-margin-right">
          <div class="page-header">
            <div class="page-title">
              <h1>Research Article
              </h1>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body inf-content">
        <div class="row">
          <?php
        if(isset($_GET['archiveID'])){

          $archiveID = $_GET['archiveID'];

          $data = $db->SELECT_ARCHIVE_RESEARCH($archiveID);
        ?>
          <div class="col-md-8">
              <div class="short-info">
                  <p><strong style="font-size: 20px; color:#313131"><?php echo $data['project_title']; ?> </strong><br></p>
                  <p class="detail-font"><?php echo $data['project_members']; ?></p>
                  <?php if (!empty($data['date_published'])) {
                    $first_published = DateTime::createFromFormat("Y-m-d", $data['date_published'])->format("d F Y");
                    echo '<p class="detail-font">Published: '.$first_published.' | Archive ID: '. $data['archive_id'] .'</p>'; 
                    } else {
                      echo '<p class="detail-font">Not yet published | Archive ID: '. $data['archive_id'] .'</p>';
                    }
                  ?>
              </div>
            <div class="form-group" style="padding-top: 1rem;">
              <iframe src="<?php echo $data['documents']; ?>" width="100%" height="900px" allowfullscreen></iframe>
              <div class="text-center">
                <a href="download_file.php?archiveID=<?= $archiveID ?>" class="download-pdf-button" download>Download PDF</a>
              </div>
            </div>
            
          </div>
          <div class="col-md-4">
            <div class="">
              <div class="page-header">
                <div class="page-title information-meta">
                  <span class="info-font text-white"><i class="ti-info text-black" style="background-color: white; border-radius: 50%; margin-right:6px"></i>Information</span>
                </div>
              </div>
            </div>
            <div class="info-container">
              <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Details</p>
              <ul>
                <li class="info-meta"><label>Course:</label><?= $data['course_name'] ?></li>
                <li class="info-meta"><label>Department:</label><?= $data['name'] ?></li>
                <li class="info-meta"><label>Contact Email:</label><a href="view_profile.php?contact_email=<?= $data['research_owner_email'] ?>"><?= $data['research_owner_email'] ?><i class="ti-arrow-top-right"></i></a></li>
              </ul>
            </div>
            <div class="info-container">
              <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Publication History</p>
              <ul>
                <li class="info-meta"><label>Project Year:</label><?= $data['project_year'] ?></li>
                <li class="info-meta"><label>Date Uploaded:</label><?= (new DateTime($data['dateOFSubmit']))->format("d F Y") ?></li>
                <li class="info-meta"><label>Date Published:</label>
                  <?php if (!empty($data['date_published'])) {
                    $first_published = DateTime::createFromFormat("Y-m-d", $data['date_published'])->format("d F Y");
                    echo $first_published; } 
                    else {
                      echo "Not yet";
                    }
                  ?>
                </li>
              </ul>
            </div>
            <?php
              if ($data['keywords'] !== ''){
                echo '<div class="info-container">
                        <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Keywords</p>
                        <ul class="ul-keywords">';
                        $keywords = explode(',', $data['keywords']);
                        foreach ($keywords as $keyword) {
                          echo '<li class="info-meta"><a href="all_project_list.php?keywords='.$keyword.'"><span class="info-keywords">'.$keyword.'</span></a></li>';
                        }
                        echo '</ul>
                      </div>';
              }
            ?>
          </div>
          <?php 
          } else {
            echo "<div class='col-md-12'>
                    <p style='text-align: center'>No research found.</p>
                  </div>";
          }
          ?>
          </div>
        </div>
      </div>
    </div>
  <?php include 'templates/footer.php'; ?>
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