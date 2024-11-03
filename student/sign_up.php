<?php

include '../connection/config.php';
$db = new Database();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


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
        <a href="../index.html">
          <img src="images/logo2.png">
        </a>
      </div>
      <div class="nav-side">
        <div class="search-bar m-r-16">
            <input id="searchInput" name="searchInput" type="text" class="form-control" placeholder="Search...">
            <button class="search-btn" id="search-btn"><i class="ti-search"></i></button>
        </div>
        <div class="nav-login">
            <a href="login.php" class="login-btn">Log in</a>
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
            <div class="sign-up-container">
              <form class="form-container" action="../php/student_registerCode.php" method="POST">
                <h4>Sign up now</h4>
                <div class="row">
                  <div class="col-sm-12 col-md-4 col-xl-4">
                    <div class="form-input">
                      <label for="snumber">Student No.</label>
                      <input type="text" name="snumber" id="snumber" minlength="10" maxlength="10" required>
                      <span id="snumber-error" class="error-message" style="color: #a33333;"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="firstname">First name</label>
                      <input type="name" name="firstname" id="firstname" required>
                      <span id="firstname-error" class="error-message" style="color: #a33333;"></span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="lastname">Last name</label>
                      <input type="name" name="lastname" id="lastname" required>
                      <span id="lastname-error" class="error-message" style="color: #a33333;"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="email">Email</label>
                      <input type="email" name="email" id="email" required>
                      <span id="email-error" class="error-message" style="color: #a33333;"></span>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="pnumber">Phone Number</label>
                      <input type="number" name="pnumber" id="pnumber" required>
                      <span id="pnumber-error" class="error-message" style="color: #a33333;"></span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="department">Department</label>
                      <select name="department" id="department" required>
                        <option value=""></option>
                      <?php 
                        $res = $db->showDepartments_WHERE_ACTIVE();

                        foreach ($res as $item) {
                        echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                        }
                      ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="course">Course</label>
                      <select name="course" id="course" required>
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="password">Password</label>
                      <input type="password" name="password" id="password" minlength="8" required>
                      <span  id="password-error" class="error-message m-t-2">Use 8 or more characters with a mix of letters, numbers, & symbols</span>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="form-input">
                      <div class="flex align-items-center">
                        <button type="submit" name="sign-up" class="sign-up-btn">Sign up</button>
                        <p class="m-0 ml-4">Already have an account? <a class="login-link" href="login.php">Log in</a></p>
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
document.getElementById("snumber").addEventListener("input", validateStudentNo);

function validateStudentNo() {
  const studentNo = document.getElementById("snumber").value;
  const errorMessage = document.getElementById("snumber-error");

  errorMessage.textContent = "";

  fetch("../php/checkStudentNo.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `snumber=${encodeURIComponent(studentNo)}`
  })
  .then(response => response.json())
  .then(data => {
    console.log(data);
    if (data.exists === true) {
      $('#snumber').css('background-image', 'url("../images/close.png")');
      if (data.message !== null) {
        errorMessage.textContent = data.message;
      }
    } else {
      $('#snumber').css('background-image', 'url("../images/checked.png")');
      errorMessage.textContent = "";
    }
  })
  .catch(error => console.error("Error:", error));
}
document.getElementById("email").addEventListener("input", validateEmailAddress);

function validateEmailAddress() {
  const emailNo = document.getElementById("email").value;
  const errorMessage = document.getElementById("email-error");

  errorMessage.textContent = "";

  fetch("../php/checkEmailAddress.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `email=${encodeURIComponent(emailNo)}`
  })
  .then(response => response.json())
  .then(data => {
    console.log(data);
    if (data.exists === true) {
      $('#email').css('background-image', 'url("../images/close.png")');
      if (data.message !== null) {
        errorMessage.textContent = "Email address already in use.";
      }
    } else {
      $('#email').css('background-image', 'url("../images/checked.png")');
      errorMessage.textContent = "";
    }
  })
  .catch(error => console.error("Error:", error));
}
document.getElementById("password").addEventListener("input", validatePassword);

function validatePassword() {
  const password = document.getElementById("password").value;
  const errorMessage = document.getElementById("password-error");

  errorMessage.textContent = "";

  const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  
  if (!passwordPattern.test(password)) {
    errorMessage.textContent = "Password must be at least 8 characters with uppercase, lowercase, number & symbol.";
    $(`#password`).css('background-image', 'url("../images/close.png")');
  } else {
    errorMessage.textContent = "";
    $(`#password`).css('background-image', 'url("../images/checked.png")');
  }
}



  const searchInput = document.getElementById("searchInput");
  const searchButton = document.getElementById("search-btn");

  searchButton.addEventListener("click", () => {
      if(searchInput.value) {
          window.location.href = `all_project_list.php?searchInput=${encodeURIComponent(searchInput.value)}`;
      } else {
          alert("Please enter a research title");
      }
  });

  $("#department").change(function(){
    var department = $(this).val();

    if(department != " "){
    $.ajax({
      url:"show_course.php",
      method:"POST",
      data:{"send_department_set":1, "send_department":department},

      success:function(data){
        $("#course").html(data);
        $("#course").css("display","block");
      }
    });
  }else{
    $("#course").css("display","none");
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
</script>
</body>
</html>