<?php

include '../connection/config.php';
error_reporting(0);

session_start();

if(isset($_SESSION['auth_user']['admin_id']))
header("location:research-papers.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Account | EARIST Repository</title>
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
  <!-- Header-->
  <div class="header">
    <div class="nav-header">
      <div class="logo">
        <a href="../index.php">
          <img src="images/logo2.webp">
        </a>
      </div>
      <div class="nav-side">
        <div class="search-bar m-r-16">
            <input id="searchInput" name="searchInput" type="text" class="form-control" placeholder="Search...">
            <button class="search-btn" id="search-btn"><i class="ti-search"></i></button>
        </div>
        <div class="nav-signup">
            <a href="#" class="signup-btn" onclick="logout();">Log out</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Index Content -->
  <main>
    <div class="content-wrapper h-100">
      <div class="col-xl-12 col-md-12-col sm-12">
        <div class="row">
          <div class="col-sm-12 col-md-4 col-xl-6">
              <div class="intro">
                <h2>Archive with Ease</h2>
                <p>Access a full of research resources and manage your work efficiently.</p>
              </div>
          </div>
          <div class="col-sm-12 col-md-8 col-xl-6">
            <div class="log-in-container">
              <form class="form-container" action="../php/admin_verifyAccountCode.php" method="POST">
                <?php
                    if(isset($_GET['id'])){
                        $admin_id = $_GET['id']; 
                    }
                ?>
                <input type="hidden" name="redirect_to" value="<?= isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '' ?>">
                <input type="hidden" name="admin_id" value="<?= $admin_id ?>">
                <h4>Account Verification</h4>
                <p>Please enter the code that we sent to you in your email address.</p>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="verification_number">OTP Code</label>
                      <input type="number" name="verification_number" id="verification_number" required>
                      <span id="error-code" style="color: red; font-size: 11px; display: none;"></span>
                      <span id="resend-container">
                        <a href="../php/resend_code.php" onclick="startCountdown()" id="resend-link" style="color: #666; font-size: 11px; text-align:end">Resend</a>
                        <span id="countdown-timer" style="color: #666; font-size: 11px; display: none;"></span>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <div class="flex align-items-center">
                        <button name="verify" type="submit" class="login-btn">Verify now</button>
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
<script>
const resendLink = document.getElementById("resend-link");
const countdownTimer = document.getElementById("countdown-timer");

const countdownDuration = 60;
const expirationTimeKey = "resendExpirationTime";

const savedExpirationTime = localStorage.getItem(expirationTimeKey);
if (savedExpirationTime) {
    const remainingTime = Math.floor((savedExpirationTime - Date.now()) / 1000);
    if (remainingTime > 0) {
        startCountdown(remainingTime);
    } else {
        localStorage.removeItem(expirationTimeKey); 
    }
}

function startCountdown(remainingTime = countdownDuration) {

    resendLink.style.display = "none";
    countdownTimer.style.display = "inline";

    const expirationTime = Date.now() + remainingTime * 1000;
    localStorage.setItem(expirationTimeKey, expirationTime);

    const interval = setInterval(() => {
        const timeLeft = Math.floor((expirationTime - Date.now()) / 1000);
        if (timeLeft > 0) {
            countdownTimer.innerText = `Resend available in ${timeLeft}s`;
        } else {
            clearInterval(interval);
            countdownTimer.style.display = "none";
            resendLink.style.display = "inline";
            localStorage.removeItem(expirationTimeKey);
        }
    }, 1000);
}

resendLink.addEventListener("click", (e) => {
    startCountdown();
});
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("search-btn");

    searchButton.addEventListener("click", () => {
        if(searchInput.value) {
            window.location.href = `all_project_list.php?searchInput=${encodeURIComponent(searchInput.value)}`;
        } else {
            alert("Please enter a research title");
        }
    });
    
    function logout() {
    window.location.href = 'admin_logout.php ';
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