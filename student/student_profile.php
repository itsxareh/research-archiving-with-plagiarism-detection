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

if (isset($_POST['upload'])) {
  $student_id = $_SESSION['auth_user']['student_id'];

  // Define the directory where you want to save the images
  $uploadDirectory = '../imageFiles/'; // Change this to your desired directory

  // Generate a unique filename for the updated image
  $uniqueFilename = uniqid() . '-' . $_FILES['img_student']['name'];

  // Define the full path to the saved image file
  $imagePath = $uploadDirectory . $uniqueFilename;

  // Retrieve the current image path from the database
  $currentImagePath = $db->SELECT_student_profile($student_id);

  // Delete the current image from the file system
  if (file_exists($currentImagePath)) {
      unlink($currentImagePath);
  }

  // Move the updated image to the specified directory
  if (move_uploaded_file($_FILES['img_student']['tmp_name'], $imagePath)) {
      // Image has been successfully updated in the file system

      // Update the database with the new image path
      $sql = $db->UPDATE_student_profile($imagePath, $student_id);

      if ($sql) {

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'Profile picture updated successfully.';

        $sql2 = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);

          $_SESSION['alert'] = "Success...";
          $_SESSION['status'] = "Image Updated";
          $_SESSION['status-code'] = "success";
      } else {
          $_SESSION['alert'] = "Failed!";
          $_SESSION['status'] = "Database update failed";
          $_SESSION['status-code'] = "error";
      }
  } else {
      $_SESSION['status'] = "Failed to move the uploaded image";
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

    <title>Student Profile: EARIST Research Archiving System</title>

    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo2.png">
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
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">

    <style>
    .message-icon, .clock-icon, .calendar-icon {
        width: 24px;
        text-align: center;
    }
    .clock-icon {
        opacity: 0.7;
    }
    .list-group-item {
        transition: background-color 0.2s;
    }

    .list-group-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .message-actions {
        display: flex;
        flex-wrap: nowrap;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .list-group-item:hover .message-actions {
        opacity: 1;
    }

    .btn-link {
        padding: 0.25rem 0.5rem;
    }

    .btn-link:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 0.25rem;
    }
    .bg-light {
    background-color: #f8f9fa !important;
    }

    .list-group-item.bg-light {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        border-left: 3px solid #a33333;
    }
    .list-group-item-action:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .list-group-item-action .message-icon {
        transition: transform 0.2s ease;
    }

    .list-group-item-action:hover .message-icon {
        transform: translateX(2px);
    }
    </style>
</head>
<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/student_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



  <div class="content-wrap">
    <div class="container">
      <div class=" col-sm-12 col-md-12 col-lg-12">
        <div class="row">
          <div class="col-md-12">
            <div class="page-header">
              <div class="page-title">
                <h1>My account
                </h1>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-12 col-lg-12 p-0">
          <div class="row">
              <?php
            if(isset($_SESSION['auth_user']['student_id'])){

              $student_id = $_SESSION['auth_user']['student_id'];

              $data = $db->student_profile($student_id);
                
            }
            ?>

              <div class="col-sm-12 col-md-3 col-xl-3">
                <div class="profile-menu">
                  <ul>
                    <li><a class="menu-accountInfo" href="student_profile.php?menuTab=accountInfo">Personal Information</a></li>
                    <li><a class="menu-accountSecurity" href="student_profile.php?menuTab=accountSecurity">Security</a></li>
                    <li><a class="menu-accountInbox" href="student_profile.php?menuTab=accountInbox">Inbox</a></li>
                    <li><a class="menu-accountActivityLogs" href="student_profile.php?menuTab=accountActivityLogs">Activity Logs</a></li>
                  
                  </ul>
                </div>
              </div>
              <div class="col-sm-12 col-md-9 col-xl-9">
                <div class="content">
                  <?php 
                    $page = isset($_GET['menuTab']) ? $_GET['menuTab'] : 'accountInfo';
                    include $page.'.php';
                  ?>
                </div>
              </div>
            </div>
        </div>
  </div>                                        
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  
  $('.menu-<?php echo isset($_GET['menuTab']) ? $_GET['menuTab'] : '' ?>').addClass('active')
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