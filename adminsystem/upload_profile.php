<?php 
session_start();
include '../connection/config.php';
$db = new Database();

if (isset($_FILES['img_student'])) {
  $admin_id = $_SESSION['auth_user']['admin_id'];

  $uploadDirectory = '../imageFiles/'; 

  $uniqueFilename = uniqid() . '-' . $_FILES['img_student']['name'];
  $imagePath = $uploadDirectory . $uniqueFilename;

  $currentImagePath = $db->SELECT_admin_profile($admin_id);

  if (file_exists($currentImagePath)) {
      unlink($currentImagePath);
  }

  if (move_uploaded_file($_FILES['img_student']['tmp_name'], $imagePath)) {
      $sql = $db->UPDATE_admin_profile($imagePath, $admin_id);

      if ($sql) {

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'Profile picture updated successfully.';

        $sql2 = $db->admin_Insert_NOTIFICATION($logs, $date, $time, $admin_id);

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