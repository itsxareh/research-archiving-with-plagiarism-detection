<?php
if($_SESSION['auth_user']['admin_id']==0){
  echo"<script>window.location.href='index.php'</script>";
  
}
if (isset($_POST['updateInfo'])) {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $pnumber = $_POST['pnumber'];

    // Fetch the current data from the database
    $currentData = $db->admin_profile($admin_id);

    // Check if the form data is different from the current data
    if ($fname !== $currentData['first_name'] ||
        $mname !== $currentData['middle_name'] ||
        $lname !== $currentData['last_name']) {

        // Prepare and execute the SQL update query
        $stmt = $db->UPDATE_admin_info_onSETTINGS($fname, $mname, $lname, $pnumber, $adminID);

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
    <div class="edit-info-details" id="edit-info" style="display: none;">
        <form action="" method="POST">
            <div class="close-right">
                <button class="close-button info-label">Close <span><i class="ti-close m-l-4"></i></span></button>
            </div>
            <div class="edit-info-container">
                <div class="item-detail">
                    <label class="info-label" for="first_name">First Name</label>
                    <input class="info-input" type="text" id="first_name" name="first_name" value="<?php echo $data['first_name'];?>">
                </div>
                <div class="item-detail">
                    <label class="info-label" for="middle_name">Middle Name</label>
                    <input class="info-input" type="text" id="middle_name" name="middle_name" value="<?php echo $data['middle_name'];?>">
                </div>
                <div class="item-detail">
                    <label class="info-label" for="last_name">Last Name</label>
                    <input class="info-input" type="text" id="last_name" name="last_name" value="<?php echo $data['last_name'];?>">
                </div>
                <div class="item-detail">
                <label class="info-label" for="pnumber">Phone number</label>
                <input class="info-input" type="text" id="pnumber" name="pnumber" value="<?php echo $data['phone_number'];?>">
                </div>
            </div>
            <div class="submit-container">
                <button class="close-button info-label m-r-8 m-t-10">Close</button>
                <button class="update-button info-label m-t-10" name="updateInfo">Update</button>
            </div>
        </form>
    </div>
    <button class="edit-button info-label">Edit<img class="m-l-2" src="../images/edit.svg" style="width: .875rem; height: .875rem"></span></button>
</div>
<hr class="divider-space">
<strong>Email Address</strong><br>
<div class="email-info">
    <div class="email-details">
        <div class="item-detail">
            <span class="info-label">Primary email is used for account-related notifications and password reset.</span>
            <span class="profile-email"><img class="m-r-8 m-l-2" src="../images/email.svg" style="width: .875rem; height: .875rem"></i><?php echo $data['admin_email']; ?></span>
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