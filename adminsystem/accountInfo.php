<?php
if($_SESSION['auth_user']['admin_id']==0){
  echo"<script>window.location.href='index.php'</script>";
  
}
if (isset($_POST['updateInfo'])) {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $department = $_POST['department'];
    $course = $_POST['department_course'];

    // Fetch the current data from the database
    $currentData = $db->student_profile($admin_id);

    // Check if the form data is different from the current data
    if ($fname !== $currentData['first_name'] ||
        $mname !== $currentData['middle_name'] ||
        $lname !== $currentData['last_name']) {

        // Prepare and execute the SQL update query
        $stmt = $db->UPDATE_student_info_onSETTINGS($fname, $mname, $lname, $department, $course, $admin_id);

        // Check if the update was successful
        if ($stmt) {
          date_default_timezone_set('Asia/Manila');
          $date = date('F / d l / Y');
          $time = date('g:i A');
          $logs = 'You successfully updated your information.';

          $sql2 = $db->student_Insert_NOTIFICATION($admin_id, $logs, $date, $time);

            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "Update Success";
            $_SESSION['status-code'] = "success";
        } else {
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "Update Failed";
            $_SESSION['status-code'] = "error";
        }
    } else {
        // Values have not changed
        $_SESSION['alert'] = "Info";
        $_SESSION['status'] = "Nothing has changed.";
        $_SESSION['status-code'] = "info";
    }
}
if (isset($_FILES['img_student'])) {
  $admin_id = $_SESSION['auth_user']['admin_id'];

  // Define the directory where you want to save the images
  $uploadDirectory = '../imageFiles/'; // Change this to your desired directory

  // Generate a unique filename for the updated image
  $uniqueFilename = uniqid() . '-' . $_FILES['img_student']['name'];

  // Define the full path to the saved image file
  $imagePath = $uploadDirectory . $uniqueFilename;

  // Retrieve the current image path from the database
  $currentImagePath = $db->SELECT_admin_profile($admin_id);

  // Delete the current image from the file system
  if (file_exists($currentImagePath)) {
      unlink($currentImagePath);
  }

  // Move the updated image to the specified directory
  if (move_uploaded_file($_FILES['img_student']['tmp_name'], $imagePath)) {
      // Image has been successfully updated in the file system

      // Update the database with the new image path
      $sql = $db->UPDATE_admin_profile($imagePath, $admin_id);

      if ($sql) {

        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'Profile picture updated successfully.';

        $sql2 = $db->student_Insert_NOTIFICATION($admin_id, $logs, $date, $time);

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
<strong>Basic Info</strong><br>
<div class="basic-info">
    <form id="image-upload-form" method="POST" enctype="multipart/form-data">
        <label class="change-picture" for="upload-image">
            <div class="profile">
                <img alt="" id="myImage" title="" class="profile-picture" src="<?php echo $data['admin_profile_picture']; ?>" data-original-title="Usuario">
            </div>
            <span class="info-label">Edit photo</span>
            <input id="upload-image" class="upload-image" type="file" name="img_student" onchange="previewAndUploadImage(event)" required accept="image/*">
        </label>
    </form>
    <div class="info-details">
        <div class="item-detail">
            <span class="info-label">Name:</span>
            <span class="profile-info"><?php echo $data['first_name'].' '.$data['middle_name'].' '.$data['last_name']; ?></span>
        </div>
        <div class="item-detail">
            <span class="info-label">Address:</span>
            <span class="profile-info"><?php echo $data['complete_address']; ?></span>
        </div>
        <div class="item-detail">
            <span class="info-label">Status</span>
            <span class="profile-info"><?php echo $data['verify_status']; ?></span>
        </div>
    </div>
    <div class="edit-info-details" style="display: none;">
        <form action="" method="POST">
            <div class="close-right">
                <button class="close-button info-label">Close <span><i class="ti-close m-l-4"></i></span></button>
            </div>
            <div class="edit-info-container">
                <div class="item-detail">
                    <label class="info-label" for="first_name">First Name:</label>
                    <input class="info-input" type="text" id="first_name" name="first_name" value="<?php echo $data['first_name'];?>">
                </div>
                <div class="item-detail">
                    <label class="info-label" for="middle_name">Middle Name:</label>
                    <input class="info-input" type="text" id="middle_name" name="middle_name" value="<?php echo $data['middle_name'];?>">
                </div>
                <div class="item-detail">
                    <label class="info-label" for="last_name">Last Name:</label>
                    <input class="info-input" type="text" id="last_name" name="last_name" value="<?php echo $data['last_name'];?>">
                </div>
                <div class="item-detail">
                <label class="info-label" for="complete_address">Address:</label>
                <input class="info-input" type="text" id="complete_address" name="complete_address" value="<?php echo $data['complete_address'];?>">
                </div>
            </div>
            <div class="submit-container">
                <button class="close-button info-label m-r-8 m-t-10">Close</button>
                <button class="update-button info-label m-t-10" name="updateInfo">Update</button>
            </div>
        </form>
    </div>
    <button class="edit-button info-label">Edit<span><i class="ti-pencil m-l-4"></i></span></button>
</div>
<hr class="divider-space">
<strong>Email Address</strong><br>
<div class="email-info">
    <div class="email-details">
        <div class="item-detail">
            <span class="info-label">Primary email is used for account-related notifications and password reset.</span>
            <span class="profile-email"><i class="ti-email m-r-8 m-l-2"></i><?php echo $data['admin_email']; ?></span>
        </div>
    </div>
</div>
<hr class="divider-space">




<script>
    $("#inputDepartment").change(function(){
    var department = $(this).val();

    if(department != " "){
            $.ajax({
                url:"show_course.php",
                method:"POST",
                data:{"send_department_set":1, "send_department":department},

                success:function(data){
                $("#department_course").html(data);
                $("#department_course").css("display","block");
                }
            });
            }else{
            $("#department_course").css("display","none");
            }

    });
    function previewAndUploadImage(event) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('myImage');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);

        var formData = new FormData();
        formData.append('img_student', event.target.files[0]);

        $.ajax({
            url: 'upload_profile.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                location.reload();
            }
        });
    }
    $(".edit-button").click(function() {
        $(".info-details").hide();
        $(".edit-button").hide();
        $(".edit-info-details").show();
    });

    $(".close-button").click(function(e) {
        e.preventDefault();
        $(".edit-info-details").hide();
        $(".edit-button").show();
        $(".info-details").show();
    });
</script>

<!-- 
    Common
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
-->
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