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


if (isset($_POST['updateInfo'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    $fname = $_POST['first_NAME'];
    $mname = $_POST['middle_NAME'];
    $lname = $_POST['last_NAME'];
    $cp_number = $_POST['cpnumber'];

    // Fetch the current data from the database
    $currentData = $db->student_profile($student_id);

    // Check if the form data is different from the current data
    if ($fname !== $currentData['first_name'] ||
        $mname !== $currentData['middle_name'] ||
        $lname !== $currentData['last_name'] ||
        $cp_number !== $currentData['phone_number']) {

        // Prepare and execute the SQL update query
        // $stmt = $db->UPDATE_student_info_onSETTINGS($fname, $mname, $lname, $cp_number, $student_id);

        // Check if the update was successful
        if ($stmt) {
          date_default_timezone_set('Asia/Manila');
          $date = date('F / d l / Y');
          $time = date('g:i A');
          $logs = 'You successfully updated your information.';

          $sql2 = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);

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


//UPDATE PASSWORD
if (isset($_POST['updatePword'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    $pword = md5($_POST['Cpword']);
    $npword = md5($_POST['Npword']);
    $rnpword = md5($_POST['RNpword']);

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
            $_SESSION['status'] = "New Password & Repeat Password is not the same.";
            $_SESSION['status-code'] = "error";
        }
    }else{

    $_SESSION['alert'] = "Error";
    $_SESSION['status'] = "Wrong Current Password";
    $_SESSION['status-code'] = "error";

    }

}
?>
    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8 p-r-0 title-margin-right">
                        <div class="page-header">
                            <div class="page-title">
                                <h1>Settings</h1>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                    <div class="col-lg-4 p-l-0 ">
                        <div class="page-header">
                            <div class="page-title">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Settings</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <!-- /# column -->
                </div>

<?php
  if(isset($_SESSION['auth_user']['student_id'])){

    $adminID = $_SESSION['auth_user']['student_id'];

    $data = $db->student_profile($student_id);
      
  }
?>
<form action="" method="POST" class="row g-3 needs-validation" novalidate="">
  <div class="col-md-4 position-relative">
    <label for="validationTooltip01" class="form-label">First name</label>
    <input type="text" class="form-control" name="first_NAME" id="validationTooltip01" value="<?php echo $data['first_name']; ?>" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      First Name required
    </div>
  </div>

  <div class="col-md-4 position-relative">
    <label for="validationTooltip02" class="form-label">Middle name</label>
    <input type="text" class="form-control" name="middle_NAME" id="validationTooltip02" value="<?php echo $data['middle_name']; ?>" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      Middle Name required
    </div>
  </div>

  <div class="col-md-4 position-relative">
    <label for="validationTooltip02" class="form-label">Last name</label>
    <input type="text" class="form-control" name="last_NAME" id="validationTooltip02" value="<?php echo $data['last_name']; ?>" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      Last Name required
    </div>
  </div>

  <div class="col-md-4 position-relative">
    <label for="validationTooltip02" class="form-label">Phone Number</label>
    <input type="text" class="form-control" name="cpnumber" id="validationTooltip02" value="<?php echo $data['phone_number']; ?>" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      Phone number required
    </div>
  </div>

  <div class="col-12" style="margin-top: 50px;">
    <button class="btn btn-primary" name="updateInfo">Update Information</button>
  </div>
</form>

<br><br><br>

<form action="" method="POST" class="row g-3 needs-validation" novalidate="">
  <div class="col-md-4 position-relative">
    <label for="validationTooltip01" class="form-label">Current Password</label>
    <input type="password" class="form-control" name="Cpword" id="validationTooltip01" value="" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      Current password required
    </div>
  </div>

  <div class="col-md-4 position-relative">
    <label for="validationTooltip02" class="form-label">New Password</label>
    <input type="password" class="form-control" name="Npword" id="validationTooltip02" value="" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      New password required
    </div>
  </div>

  <div class="col-md-4 position-relative">
    <label for="validationTooltip02" class="form-label">Repeat New Password</label>
    <input type="password" class="form-control" name="RNpword" id="validationTooltip02" value="" required="">
    <div class="valid-tooltip">
      Looks good!
    </div>
    <div class="invalid-tooltip">
      Repeat new password required
    </div>
  </div>

  <div class="col-12" style="margin-top: 50px;">
    <button class="btn btn-primary" name="updatePword">Update Password</button>
  </div>
</form>


                <!-- /# row -->
                <section id="main-content">
                    <div class="row">
                        <div class="col-lg-12" style="margin-top: 50px;">
                            <div id="extra-area-chart"></div>
                            <div id="morris-line-chart"></div>
                            <div class="footer">
                                <p>2024 © Admin Board. -
                                    <a href="#">example.com</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>




    <!-- Common -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>

    <!--  Peity -->
    <script src="js/lib/peitychart/jquery.peity.min.js"></script>
    <script src="js/lib/peitychart/peitychart.init.js"></script>

    <!--  Sparkline -->
    <script src="js/lib/sparklinechart/jquery.sparkline.min.js"></script>
    <script src="js/lib/sparklinechart/sparkline.init.js"></script>

    <!-- Select2 -->
    <script src="js/lib/select2/select2.full.min.js"></script>

    <!--  Validation -->
    <script src="js/lib/form-validation/jquery.validate.min.js"></script>
    <script src="js/lib/form-validation/jquery.validate-init.js"></script>

    <!--  Owl Carousel -->
    <script src="js/lib/owl-carousel/owl.carousel.min.js"></script>
    <script src="js/lib/owl-carousel/owl.carousel-init.js"></script>

    <!-- JS Grid -->
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

    <!--  Nestable -->
    <script src="js/lib/nestable/jquery.nestable.js"></script>
    <script src="js/lib/nestable/nestable.init.js"></script>

    <!-- Sweet Alert -->
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>



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