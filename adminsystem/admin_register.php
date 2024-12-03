<?php

include '../connection/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

// if($_SESSION['auth_user']['admin_id']==0){
//     echo"<script>window.location.href='index.php'</script>";
    
//   }

// ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Register - EARIST Research Archiving System</title>

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

    <!-- Styles -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="bg-primary" style="background-color: #d34848!important;" >

    <div class="unix-login">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="dashboard.php"><span>EARIST Research Archiving System</span></a>
                        </div>
                        <div class="login-form">
                            <h4>Admin Register</h4>

                            <form action="../php/admin_registerCode.php" method="POST" enctype="multipart/form-data" class="myForm">
                                <label>Full Name</label>
                                <div class="form-inline">
                                <input type="text" style="width: 185px;" class="form-control" placeholder="First Name" name="f_name" required>
                                <span style="margin-right: 10px;"></span><!-- Add a gap here -->
                                <input type="text" style="width: 185px;" class="form-control" placeholder="Middle Name(optional)" name="m_name">
                                <span style="margin-right: 10px;"></span><!-- Add a gap here -->
                                <input type="text" style="width: 185px;" class="form-control" placeholder="Last Name" name="l_name" required>
                                </div><br>
                                <div class="form-group">
                                    <label>Complete Address</label>
                                    <input type="text" class="form-control" placeholder="Complete Address" name="C_address" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" placeholder="09*********" name="cpNum" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" placeholder="johndoe@gmail.com" name="eMail" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control" placeholder="Password" name="pword" required>
                                </div>
                                <div class="form-group">
                                    <label>Repeat Password</label>
                                    <input type="password" class="form-control" placeholder="Confirm Password" name="cpword" required>
                                </div>
                                <div class="form-group">
                                    <label>Import Picture</label>
                                    <input type="file" class="form-control" accept="image/*" name="admin_pic" required>
                                </div>
                                <!-- <div class="checkbox">
                                    <label>
										<input type="checkbox"> Agree the terms and policy 
									</label>
                                </div> -->

                                <button name="register" class="btn btn-primary btn-flat m-b-30 m-t-30" style="background: #d34848;">Register</button>
                                
                                <div class="register-link m-t-15 text-center">
                                    <a href="dashboard.php"> Go Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
	<script src="js/scripts.js"></script>

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