<?php

include '../connection/config.php';
error_reporting(0);

session_start();

if(isset($_SESSION['auth_user']['student_id']))
header("location:all_project_list.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EARIST Research Archiving System</title>
  <link rel="shortcut icon" href="images/logo1.png">
  <link rel="stylesheet" href="../css/login-sign-up.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link href="css/lib/themify-icons.css" rel="stylesheet">
  <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
  
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="js/lib/sweetalert/sweetalert.min.js"></script>
  <script src="js/lib/sweetalert/sweetalert.init.js"></script>
</head>
<body>  
  <!-- Header-->
  <div class="header">
    <div class="nav-header">
      <div class="logo">
        <a href="../index.php">
          <img src="images/logo2.png">
        </a>
      </div>
      <div class="nav-side">
        <div class="search-bar m-r-16">
            <input id="searchInput" name="searchInput" type="text" class="form-control" placeholder="Search...">
            <button class="search-btn" id="search-btn"><i class="ti-search"></i></button>
        </div>
        <div class="nav-signup">
            <a href="login.php" class="signup-btn">Log in</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Index Content -->
  <main>
    <div class="content-wrapper h-100">
      <div class="col-xl-12 col-md-12-col sm-12">
        <div class="row p-4">
          <div class="col-sm-12 col-md-4 col-xl-6">
              <div class="intro">
                <h2>Archive with Ease</h2>
                <p>Access a full of research resources and manage your work efficiently.</p>
              </div>
          </div>
          <div class="col-sm-12 col-md-8 col-xl-6">
            <div class="log-in-container">
              <form class="form-container" action="../php/student_forgotPassword.php" method="POST">
                <h4>Forgot password</h4>
                <p>Please enter your registered email address. We’ll send you a code to reset your password..</p>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="email">Email address</label>
                      <input type="email" name="email" id="email" required>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="flex align-items-center">
                      <button name="recover-now" type="submit" class="login-btn">Recover now</button>
                      <p class="m-0 ml-4">Remember now? <a class="signup-link" href="login.php">Log in</a></p>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
<script>
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("search-btn");

    searchButton.addEventListener("click", () => {
        if(searchInput.value) {
            window.location.href = `all_project_list.php?searchInput=${encodeURIComponent(searchInput.value)}`;
        } else {
            alert("Please enter a research title");
        }
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