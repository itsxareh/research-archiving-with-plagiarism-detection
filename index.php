<?php

include 'connection/config.php';
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
  <link rel="shortcut icon" href="student/images/logo1.png">
  <link rel="stylesheet" href="../css/index.css">
  <link href="css/lib/themify-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

</head>
<body>  
  <!-- Header-->
  <div class="header">
    <div class="nav-header">
      <div class="logo">
        <a href="index.php"><img src="images/logo2.png"></a>
      </div>
      <div class="nav-side">
        <?php
          if(isset($_SESSION['auth_user']['student_id'])) {
              $student_id = $_SESSION['auth_user']['student_id'];
              $result = $db->student_profile($student_id);
           
        ?>
        <div class="dropdown dib">
          <div class="header-icon" data-toggle="dropdown">
              <span class="user-avatar">Logged in as <?php echo $result['first_name'];?> <?php echo $result['last_name'];?>
                  <i class="ti-angle-down f-s-10"></i>
              </span>
              <div class="drop-down dropdown-profile dropdown-menu dropdown-menu-right">
                  <div class="dropdown-content-body">
                      <ul>
                          <li>
                              <a href="#" onclick="profile();">
                                  <i class="ti-user"></i>
                                  <span>My Info</span>
                              </a>
                          </li>
                          
                          <li>
                              <a href="#" onclick="logout();">
                                  <i class="ti-power-off"></i>
                                  <span>Logout</span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </div>
          </div>
      </div>
      <?php 
      } else { 
      ?>
      <div class="nav-login">
          <a href="student/login.php" class="login-btn">Log in</a>
      </div>
      <?php 
      } 
      ?>
      </div>
    </div>
  </div>
  <!-- Index Content -->
  <main>
    <div class="content-wrapper h-100">
        <div class="col-sm-12 col-xl-12 col-md-12">
            <div class="row p-4 justify-content-center">
                <div class="col-sm-12 col-md-8 col-xl-8 text-center intro">
                    <h2>Explore a world of knowledge and uncover the insights that drive discovery</h2>
                    <p>Discover a world of research and ideas. Access countless academic papers, archive your findings, and stay organized. Start building your knowledge repository today.</p>
                </div>
                <div class="col-sm-12 col-md-8 col-xl-8 research-search text-center">
                    <p class="search-description">Enter research title so we could help you in your research.</p>
                    <div class="row form-input align-items-start">
                        <div class="col-sm-8 col-md-10 col-xl-10 m-t-2 p-0">
                            <input id="searchInput" type="text" placeholder="Research Paper Title">
                        </div>
                        <div class="col-sm-4 col-md-2 col-xl-2 m-t-2 p-0">
                            <button id="searchButton" class="search-btn">Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </main>
<script>
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");

    searchButton.addEventListener("click", () => {
        if(searchInput.value !== '') {
            window.location.href = `student/all_project_list.php?searchInput=${encodeURIComponent(searchInput.value)}`;
        } 
    });

    const dropdownToggle = document.querySelector(".header-icon");
    const dropdownMenu = document.querySelector(".dropdown-profile");

    dropdownToggle.addEventListener("click", (event) => {
        event.stopPropagation(); 
        dropdownMenu.classList.toggle("show"); 
    });

    document.addEventListener("click", (event) => {
        if (!dropdownToggle.contains(event.target) && dropdownMenu.classList.contains("show")) {
            dropdownMenu.classList.remove("show");
        }
    });
</script>
<script>
  function profile() {
    window.location.href = 'student/student_profile.php?menuTab=accountInfo';
  }
  function logout() {
    window.location.href = 'logout.php ';
  }
</script>
</body>
</html>