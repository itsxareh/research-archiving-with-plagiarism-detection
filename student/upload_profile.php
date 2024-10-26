<?php 
session_start();
include '../connection/config.php';
$db = new Database();

if (isset($_FILES['img_student'])) {
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