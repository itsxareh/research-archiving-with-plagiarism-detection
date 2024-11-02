<?php

include '../connection/config.php';
error_reporting(0);

session_start();

if(isset($_SESSION['auth_user']['student_id']))
header("location:all_project_list.php");


if (!isset($_SESSION['email'])){
  header("location: login.php");
  exit();
} else {
  $email = $_SESSION['email'];
}

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
        <a href="../index.html">
          <img src="images/logo2.png">
        </a>
      </div>
      <!-- <div class="nav-side">
        <div class="search-bar m-r-16">
            <input id="searchInput" name="searchInput" type="text" class="form-control" placeholder="Search...">
            <button class="search-btn" id="search-btn"><i class="ti-search"></i></button>
        </div>
        <div class="nav-signup">
            <a href="login.php" class="signup-btn">Log in</a>
        </div>
      </div> -->
    </div>
  </div>
  <!-- Index Content -->
  <main>
    <div class="content-wrapper h-100">
      <div class="col-xl-12 col-md-12-col sm-12">
        <div class="row p-4">
          <div class="col-sm-12 col-md-6 col-xl-6">
              <div class="intro">
                <h2>Archive with Ease</h2>
                <p>Access a full of research resources and manage your work efficiently.</p>
              </div>
          </div>
          <div class="col-sm-12 col-md-6 col-xl-6">
            <div class="log-in-container">
              <form class="form-container" action="../php/student_recoverAccount.php" method="POST">
                
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email);?>">
                <h4>Recover my account</h4>
                <p>Please enter the code that we sent to you in your email address.</p>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="code">OTP Code</label>
                      <input type="number" name="code" id="code" minlength="6" maxlength="6" required>
                      <span id="resend-container">
                        <a href="../php/resend_code.php" onclick="startCountdown()" id="resend-link" style="color: #666; font-size: 11px; text-align:end">Resend</a>
                        <span id="countdown-timer" style="color: #666; font-size: 11px; display: none;"></span>
                    </span>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="flex align-items-center">
                      <button name="recover" type="submit" class="login-btn">Recover</button>
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