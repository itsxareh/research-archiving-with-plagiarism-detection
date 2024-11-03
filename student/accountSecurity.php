<?php
if($_SESSION['auth_user']['student_id']==0){
  echo"<script>window.location.href='login.php'</script>";
  
}

//UPDATE PASSWORD
if (isset($_POST['update'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    $pword = md5($_POST['cpassword']);
    $npword = md5($_POST['npassword']);
    $rnpword = md5($_POST['cnpassword']);

    // Fetch the current data from the database
    $currentData = $db->student_profile($student_id);

    $password = $currentData['student_password'];

    if ($pword == $password) {
        if ($npword == $rnpword) {

            // Prepare and execute the SQL update query
        $stmt = $db->UPDATE_student_password($npword, $student_id);

                // Check if the update was successful
                if ($stmt) {
                  date_default_timezone_set('Asia/Manila');
                  $date = date('F / d l / Y');
                  $time = date('g:i A');
                  $logs = 'You successfully updated your password.';

                  $sql2 = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);
                  
                    $_SESSION['alert'] = "Success";
                    $_SESSION['status'] = "Password Updated!";
                    $_SESSION['status-code'] = "success";
                } else {
                    $_SESSION['alert'] = "Error";
                    $_SESSION['status'] = "Update Failed";
                    $_SESSION['status-code'] = "error";
                }
            
        }else{
            $_SESSION['alert'] = "Error";
            $_SESSION['status'] = "New Password & Confirm New Password is not the same.";
            $_SESSION['status-code'] = "error";
        }
    }else{

    $_SESSION['alert'] = "Error";
    $_SESSION['status'] = "Invalid Current Password";
    $_SESSION['status-code'] = "error";

    }

}

if(isset($_SESSION['auth_user']['student_id'])){

  $adminID = $_SESSION['auth_user']['student_id'];

  $data = $db->student_profile($student_id);
    
}
?>
<div class="edit-info-details">
        <form action="" method="POST" class="needs-validation">
            <div class="col-sm-12 col-md-12 col-xl-12">
              <div class="row">
                <div class="col-sm-12 col-md-12 col-xl-12">
                  <div class="row">
                    <div class="item-detail col-sm-12 col-md-6 col-xl-6 md:p-0">
                        <label class="info-label" for="cpassword">Current Password:</label>
                        <input class="info-input" type="password" id="cpassword" name="cpassword" minlength = 8 maxlength = 16 required>
                        <span  id="cpassword-error" class="error-message m-t-2">Use 8 or more characters with a mix of letters, numbers, & symbols</span>
                      </div>
                    <div class="item-detail col-sm-12 col-md-6 col-xl-6 md:p-0">
                        <label class="info-label" for="npassword">New password:</label>
                        <input class="info-input" type="password" id="npassword" name="npassword" minlength = 8 maxlength = 16 required>
                        <span  id="npassword-error" class="error-message m-t-2">Use 8 or more characters with a mix of letters, numbers, & symbols</span>
                      </div>
                    <div class="item-detail col-sm-12 col-md-6 col-xl-6 md:p-0">
                        <label class="info-label" for="cnpassword">Confirm New Password:</label>
                        <input class="info-input" type="password" id="cnpassword" name="cnpassword" minlength = 8 maxlength = 16 required>
                        <span  id="cnpassword-error" class="error-message m-t-2">Use 8 or more characters with a mix of letters, numbers, & symbols</span>
                      </div>
                  </div>
                  <div class="submit-container">
                      <button class="update-button info-label m-t-10" name="update">Update</button>
                  </div>
                </div>
              </div>
            </div>
        </form>
    </div>
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>



<script>
document.getElementById("cpassword").addEventListener("input", () => validatePassword("cpassword"));
document.getElementById("npassword").addEventListener("input", () => validatePassword("npassword"));
document.getElementById("cnpassword").addEventListener("input", validateConfirmPassword);

function validatePassword(field) {
  const password = document.getElementById(field).value;
  const errorMessage = document.getElementById(`${field}-error`);

  const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  errorMessage.textContent = "";
  
  if (!passwordPattern.test(password)) {
    errorMessage.textContent = "Password must be at least 8 characters with uppercase, lowercase, number & symbol.";
    $(`#${field}`).css('background-image', 'url("../images/close.png")');
  } else {
    errorMessage.textContent = "";
    $(`#${field}`).css('background-image', 'url("../images/checked.png")');
  }

  if (field === "npassword") {
    validateConfirmPassword();
  }
}

function validateConfirmPassword() {
  const newPassword = document.getElementById("npassword").value;
  const confirmPassword = document.getElementById("cnpassword").value;
  const errorMessage = document.getElementById("cnpassword-error");

  if (confirmPassword !== newPassword) {
    errorMessage.textContent = "Passwords do not match";
    $('#cnpassword').css('background-image', 'url("../images/close.png")');
  } else {
    errorMessage.textContent = "";
    $('#cnpassword').css('background-image', 'url("../images/checked.png")');
  }
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