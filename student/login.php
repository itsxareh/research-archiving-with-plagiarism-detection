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
  <title>EARIST Repository</title>
  <link rel="shortcut icon" href="images/logo2.webp">
  <link rel="stylesheet" href="../css/login-sign-up.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link href="css/lib/themify-icons.css" rel="stylesheet">
  <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
  
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="js/lib/sweetalert/sweetalert.min.js"></script>
  <script src="js/lib/sweetalert/sweetalert.init.js"></script>
</head>
<body>  
  <div class="loading-overlay">
    <div class="loading-spinner"></div>
  </div>
  <!-- Header-->
  <div class="header">
    <div class="nav-header">
      <div class="logo">
        <a href="../index.php"><img src="../images/logo2.webp"></a>
      </div>
      <div class="nav-side">
        <div class="search-bar m-r-16">
            <input id="searchInput" name="searchInput" type="text" class="form-control" placeholder="Search...">
            <button class="search-btn" id="search-btn" style="background-color: transparent">
                <img style="width: 1.275rem; height: 1.275rem; " src="../../images/search.svg" alt="">
            </button>
        </div>
        <div class="nav-signup">
            <a href="sign_up.php" class="signup-btn no-wrap">Sign up</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Index Content -->
  <main>
    <div class="content-wrapper h-100">
      <div class="col-xl-12 col-md-12-col sm-12">
        <div class="row login-content" style="padding: 1.5rem">
          <div class="col-sm-12 col-md-6 col-xl-6">
              <div class="intro">
                <h2>Archive with Ease</h2>
                <p>Access a full of research resources and manage your work efficiently.</p>
              </div>
          </div>
          <div class="col-sm-12 col-md-6 col-xl-6">
            <div class="log-in-container">
              <form class="form-container" id="loginForm" method="POST">
                <input type="hidden" name="redirect_to" value="<?= isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '' ?>">
                <h4>Login</h4>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="email">Email</label>
                      <input type="email" name="email" id="email" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="password">Password</label>
                      <input type="password" name="password" id="password" required>
                      <a href="forgot_password.php" style="color: #666; font-size: 11px; text-align:end ">Forgot password</a>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <div class="flex align-items-center">
                        <button name="submit" type="submit" class="login-btn" style="text-wrap: nowrap;" id="loginBtn">Log in</button>
                        <p style="font-size: 12px; margin-left: 1.5rem" class="mb-0 no-account">Don't have an account? <a class="signup-link" href="sign_up.php">Sign up</a></p>
                      </div>
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
  <?php require_once 'templates/footer.php'; ?>
<script>
$('#loginForm').on('submit', function(e) {
    e.preventDefault();

    const loginBtn = $('#loginBtn');
    loginBtn.prop('disabled', true);  
    
    $.ajax({
        url: '../php/student_loginCode.php',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            const data = JSON.parse(response);
            if (data.status_code === 'success') {
              window.location.href = data.redirect;
            } else if (data.status_code === 'info') {
              if (data.redirect) {
                swal({
                  title: data.alert,
                  text: data.status,
                  icon: data.status_code,
                }, function(isConfirm) {
                  if (isConfirm) {
                    window.location.href = data.redirect;
                  }
                });
              } else {
                loginBtn.prop('disabled', false);
                sweetAlert(data.alert, data.status, data.status_code);
              }
            } else {
              loginBtn.prop('disabled', false);
              sweetAlert(data.alert, data.status, data.status_code);
            }
        },
        error: function() {
            sweetAlert('Error', 'Something went wrong. Please try again.', 'error');
            loginBtn.prop('disabled', false);
        }
    });
});

document.querySelector('form').addEventListener('submit', function() {
    document.querySelector('button[type="submit"]').disabled = true;
});
const searchInput = document.getElementById("searchInput");
const searchButton = document.getElementById("search-btn");

searchInput.addEventListener("keydown", (event) => {
  if (event.key === "Enter") {
      event.preventDefault();
      searchButton.click();
  }
});

searchButton.addEventListener("click", () => {
  if (searchInput.value.length > 0) {
      window.location.href = `all_project_list.php?searchInput=${encodeURIComponent(searchInput.value)}`;
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