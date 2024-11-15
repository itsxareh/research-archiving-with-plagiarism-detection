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

if (isset($_POST['upload'])) {
  $admin_id = $_SESSION['auth_user']['admin_id'];

  // Define the directory where you want to save the images
  $uploadDirectory = '../imageFiles/'; // Change this to your desired directory

  // Generate a unique filename for the updated image
  $uniqueFilename = uniqid() . '-' . $_FILES['img_admin']['name'];

  // Define the full path to the saved image file
  $imagePath = $uploadDirectory . $uniqueFilename;

  // Retrieve the current image path from the database
  $currentImagePath = $db->SELECT_admin_profile($admin_id);

  // Delete the current image from the file system
  if (file_exists($currentImagePath)) {
      unlink($currentImagePath);
  }

  // Move the updated image to the specified directory
  if (move_uploaded_file($_FILES['img_admin']['tmp_name'], $imagePath)) {
      // Image has been successfully updated in the file system

      // Update the database with the new image path
      $sql = $db->UPDATE_admin_profile($imagePath, $admin_id);

      if ($sql) {

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'Profile picture updated successfully.';

        $sql2 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);

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

    <title>Admin Profile: EARIST Research Archiving System</title>

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
            if(isset($_SESSION['auth_user']['admin_id'])){

              $admin_id = $_SESSION['auth_user']['admin_id'];

              $data = $db->admin_profile($admin_id);
                
            }
            ?>

              <div class="col-sm-12 col-md-3 col-xl-3">
                <div class="profile-menu">
                  <ul>
                    <li><a class="menu-accountInfo" href="admin_profile.php?menuTab=accountInfo"><span>Personal Information</span></a></li>
                    <li><a class="menu-accountSecurity" href="admin_profile.php?menuTab=accountSecurity"><span>Security</span></a></li>
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