<?php

include '../connection/config.php';
error_reporting(0);

session_start();

if(isset($_SESSION['auth_user']['student_id']))
header("location:all_project_list.php");

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
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
    <div class="nav-header h-100">
      <div class="logo">
        <a href="../index.php"><img src="../images/logo2.webp"></a>
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
        <div class="row">
          <div class="col-sm-12 col-md-4 col-xl-6">
              <div class="intro">
                <h2>Archive with Ease</h2>
                <p>Access a full of research resources and manage your work efficiently.</p>
              </div>
          </div>
          <div class="col-sm-12 col-md-8 col-xl-6">
            <div class="log-in-container">
              <form class="form-container" action="../php/student_changePassword.php" method="POST">
                <input type="hidden" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">
                <h4>Set a new password</h4>
                <p>Please create a new password for your account.</p>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="npassword">New password</label>
                      <input type="password" name="npassword" id="npassword" minlength="8" maxlength="16" required>
                      <span  id="npassword-error" class="error-message m-t-2"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <label for="cnpassword">Confirm a new password</label>
                      <input type="password" name="cnpassword" id="cnpassword" minlength="8" maxlength="16" required>
                      <span  id="cnpassword-error" class="error-message m-t-2"></span>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <div class="flex align-items-center">
                        <button name="changePassword" type="submit" class="login-btn">Change password</button>
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
document.getElementById("npassword").addEventListener("input", validateForm);
document.getElementById("cnpassword").addEventListener("input", validateForm);

const form = document.querySelector('.form-container');
const submitButton = document.querySelector('.login-btn');

function validateForm() {
  const newPassword = document.getElementById("npassword").value;
  const confirmPassword = document.getElementById("cnpassword").value;
  const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  // Check all conditions
  const isPasswordValid = passwordPattern.test(newPassword);
  const doPasswordsMatch = newPassword === confirmPassword;
  const isFormValid = isPasswordValid && doPasswordsMatch;

  // Enable/disable submit button
  submitButton.disabled = !isFormValid;
  submitButton.style.opacity = isFormValid ? '1' : '0.5';

  // Validate individual fields
  validatePassword("npassword");
  validateConfirmPassword();
}

// Update existing validatePassword and validateConfirmPassword functions to call validateForm
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

  validateForm();
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
  validateForm();
}

// Add form submission handler
form.addEventListener('submit', (e) => {
  e.preventDefault();
  
  if (!submitButton.disabled) {
    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = 'Processing...';
    
    const formData = new FormData(form);
    
    $.ajax({
      url: '../php/student_changePassword.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        try {
          const result = JSON.parse(response);
          console.log(result);
          if (result.status_code === 'success') {
            sweetAlert(result.alert, result.status, result.status_code);
            setTimeout(() => {
              window.location.href = result.redirect;
            }, 1500);
          } else {
            sweetAlert(result.alert, result.status, result.status_code);
            submitButton.disabled = false;
            submitButton.innerHTML = 'Change password';
          }
        } catch (e) {
          console.error('Error parsing response:', e);
          sweetAlert("Error!", "Something went wrong. Please try again.", "error");
          submitButton.disabled = false;
          submitButton.innerHTML = 'Change password';
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', error);
        sweetAlert("Error!", "Something went wrong. Please try again.", "error");
        submitButton.disabled = false;
        submitButton.innerHTML = 'Change password';
      }
    });
  }
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