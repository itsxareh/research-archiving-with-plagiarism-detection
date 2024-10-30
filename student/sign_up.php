<?php

include '../connection/config.php';
$db = new Database();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
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
                      <input type="text" name="snumber" id="snumber" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-6 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="firstname">First name</label>
                      <input type="text" name="firstname" id="firstname" required>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="lastname">Last name</label>
                      <input type="text" name="lastname" id="lastname" required>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="email">Email</label>
                      <input type="email" name="email" id="email" required>
                    </div>
                  </div>
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="pnumber">Phone Number</label>
                      <input type="number" name="pnumber" id="pnumber" required>
                      
                    </div>
                  </div>
                </div>
                <div class="row">
                <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="department">Department</label>
                      <select name="department" id="department">
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
                      <select name="course" id="course">
                        <option value=""></option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-md-6 col-xl-6">
                    <div class="form-input">
                      <label for="password">Password</label>
                      <input type="password" name="password" id="password" required>
                      <span class="m-t-2">Use 8 or more characters with a mix of letters, numbers, & symbols</span>
                    </div>
                  </div>
                </div>
                <div class="row mt-4">
                  <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="flex align-items-center">
                      <button type="submit" name="sign-up" class="sign-up-btn">Sign up</button>
                      <p class="m-0 ml-4">Already have an account? <a class="login-link" href="login.php">Log in</a></p>
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