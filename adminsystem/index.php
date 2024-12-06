<?php

include '../connection/config.php';
$db = new Database();
session_start();
if(isset($_SESSION['auth_user']['admin_id'])) {
  // Get user role and permissions
  $userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
  $permissions = explode(',', $userRole['permissions']);

  // Helper function to check permissions
  function hasPermit($permissions, $permissionToCheck) {
      foreach ($permissions as $permission) {
          if (strpos($permission, $permissionToCheck) === 0) {
              return true;
          }
      }
      return false;
  }

  // Check admin status and permissions
  if($_SESSION['auth_user']['admin_id'] == 0) {
      echo "<script>window.location.href='index.php'</script>";
      exit();
  } elseif(hasPermit($permissions, 'dashboard_view')) {
      header("Location: dashboard.php");
      exit();
  } elseif(hasPermit($permissions, 'student_list_view')) {
      header("Location: students.php");
      exit();
  } elseif(hasPermit($permissions, 'research_view')) {
      header("Location: all_project_list.php");
      exit();
  } elseif(hasPermit($permissions, 'department_view')) {
    header("Location: departments.php");
    exit();
  } elseif(hasPermit($permissions, 'course_view')) {
      header("Location: courses.php");
      exit();
  } elseif(hasPermit($permissions, 'role_view')) {
      header("Location: roles.php");
      exit();
  } elseif(hasPermit($permissions, 'user_view')) {
      header("Location: admins.php");
      exit(); 
  } else {
      header('Location: ../../bad-request.php');
      exit();
  }
}

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
  <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
  
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="js/lib/sweetalert/sweetalert.min.js"></script>
  <script src="js/lib/sweetalert/sweetalert.init.js"></script>
</head>
<body>  
  <main class="p-0">
    <div class="content-wrapper h-100">
      <div class="col-xl-12 col-md-12-col sm-12">
        <div class="row login-content" style="padding: 1.25rem;">
          <div class="col-sm-12 col-md-6 col-xl-6">
              <div class="intro">
                <h2>Admin Portal</h2>
                <p>Access a full of research resources and manage your work efficiently.</p>
              </div>
          </div>
          <div class="col-sm-12 col-md-6 col-xl-6">
            <div class="log-in-container">
              <form class="form-container" action="../php/admin_loginCode.php" method="POST">
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
                    </div>
                    <!-- <a href="forgot_password.php" style="color: #666; font-size: 11px; text-align:end ">Forgot password</a>-->
                  </div> 
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <div class="flex justify-content-end">
                        <button name="submit" type="submit" class="login-btn">Log in</button>
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