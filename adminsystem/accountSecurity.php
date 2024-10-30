<?php
if($_SESSION['auth_user']['admin_id']==0){
  echo"<script>window.location.href='login.php'</script>";
  
}

//UPDATE PASSWORD
if (isset($_POST['update'])) {
    $student_id = $_SESSION['auth_user']['admin_id'];
    $pword = md5($_POST['cpassword']);
    $npword = md5($_POST['npassword']);
    $rnpword = md5($_POST['cnpassword']);

    // Fetch the current data from the database
    $currentData = $db->admin_profile($admin_id);

    $password = $currentData['student_password'];

    if ($pword == $password) {
        if ($npword == $rnpword) {

            // Prepare and execute the SQL update query
        $stmt = $db->UPDATE_admin_password($npword, $admin_id);

                // Check if the update was successful
                if ($stmt) {
                  date_default_timezone_set('Asia/Manila');
                  $date = date('F / d l / Y');
                  $time = date('g:i A');
                  $logs = 'You successfully updated your password.';

                  $sql2 = $db->admin_Insert_NOTIFICATION($admin_id, $logs, $date, $time);
                  
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

if(isset($_SESSION['auth_user']['admin_id'])){

  $admin_id = $_SESSION['auth_user']['admin_id'];

  $data = $db->admin_profile($admin_id);
    
}
?>
<div class="edit-info-details">
        <form action="" method="POST" class="needs-validation">
            <div class="edit-info-container">
                <div class="item-detail">
                    <label class="info-label" for="first_name">Current Password:</label>
                    <input class="info-input" type="password" id="first_name" name="cpassword">
                </div>
                <div class="item-detail">
                    <label class="info-label" for="middle_name">New password:</label>
                    <input class="info-input" type="password" id="middle_name" name="npassword">
                </div>
                <div class="item-detail">
                    <label class="info-label" for="last_name">Confirm New Password:</label>
                    <input class="info-input" type="password" id="last_name" name="cnpassword">
                </div>
                
            </div>
            <div class="submit-container">
                <button class="update-button info-label m-t-10" name="update">Update</button>
            </div>
        </form>
    </div>
    <!-- Common
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
     Peity 
    <script src="js/lib/peitychart/jquery.peity.min.js"></script>
    <script src="js/lib/peitychart/peitychart.init.js"></script>

     Sparkline 
    <script src="js/lib/sparklinechart/jquery.sparkline.min.js"></script>
    <script src="js/lib/sparklinechart/sparkline.init.js"></script>

 Select2 
    <script src="js/lib/select2/select2.full.min.js"></script>

 Validation
    <script src="js/lib/form-validation/jquery.validate.min.js"></script>
    <script src="js/lib/form-validation/jquery.validate-init.js"></script>
  Owl Carousel 
    <script src="js/lib/owl-carousel/owl.carousel.min.js"></script>
    <script src="js/lib/owl-carousel/owl.carousel-init.js"></script>
 JS Grid 
    <script src="js/lib/jsgrid/db.js"></script>
    <script src="js/lib/jsgrid/jsgrid.core.js"></script>
    <script src="js/lib/jsgrid/jsgrid.load-indicator.js"></script>
    <script src="js/lib/jsgrid/jsgrid.load-strategies.js"></script>
    <script src="js/lib/jsgrid/jsgrid.sort-strategies.js"></script>
    <script src="js/lib/jsgrid/jsgrid.field.js"></script>
    <script src="js/lib/jsgrid/fields/jsgrid.field.text.js"></script>
    <script src="js/lib/jsgrid/fields/jsgrid.field.number.js"></script>
    <script src="js/lib/jsgrid/fields/jsgrid.field.select.js"></script>
    <script src="js/lib/jsgrid/fields/jsgrid.field.checkbox.js"></script>
    <script src="js/lib/jsgrid/fields/jsgrid.field.control.js"></script>
    <script src="js/lib/jsgrid/jsgrid-init.js"></script>

  Nestable 
    <script src="js/lib/nestable/jquery.nestable.js"></script>
    <script src="js/lib/nestable/nestable.init.js"></script>
     -->



    <script>
   var forms = document.querySelectorAll('.needs-validation')
Array.prototype.slice.call(forms)
  .forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
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