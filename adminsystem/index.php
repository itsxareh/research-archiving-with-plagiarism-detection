<?php

include '../connection/config.php';
error_reporting(0);

session_start();
if(isset($_SESSION['auth_user']['admin_id']))
header("location:dashboard.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Login - EARIST Research Archiving System</title>

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

    <!-- Styles -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    
</head>

<body style="background-color: #a33333!important;">
    <div class="content-wrap">
        <div class="flex align-items-center justify-content-center h-100 w-100">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <div class="login-title">
                    EARIST Research Archiving System
                </div>
                <div class="p-0-sm login-form">
                    <h4>Admin Login</h4>
                    <form action="../php/admin_loginCode.php" method="POST">
                        <div class="form-group">
                            <label>Email address</label>
                            <input type="email" class="form-control" name="adminEmail" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="adminPword" placeholder="Password" required>
                        </div>
                            
                            <label class="pull-right">
                                <!-- <a href="#">Forgotten Password?</a> -->
                            </label>
                        <button name="LogIn" class="btn btn-primary btn-flat m-b-30 m-t-30" style="background: #d34848;">Login</button>
                        <div class="register-link m-t-15 text-center">
                            <!-- <p>Don't have account ? <a href="admin_register.php"> Sign Up Here</a></p> -->
                        </div>
                    </form>
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