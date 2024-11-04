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
  <style type="text/css">
    .menu-container {
        position: relative;
        display: inline-block;
        text-align: left;
    }

  .menu-button {
    display: inline-flex;
    width: 100%;
    justify-content: center;
    gap: 8px; /* Adjust spacing as needed */
    border-radius: 4px;
    background-color: #ffffff;
    padding: 8px 12px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937; /* gray-900 */
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); /* shadow-sm */
    border: none;
    /* border: 1px solid #d1d5db; ring-gray-300 */
    transition: background-color 0.2s ease;
}

.menu-button:hover {
    cursor: pointer;
    background-color: #f9fafb; /* hover:bg-gray-50 */
}

/* SVG Icon styling */
.menu-icon {
    margin-right: -4px;
    height: 20px;
    width: 20px;
    color: #9ca3af; /* gray-400 */
}

/* Dropdown styling */
.dropdown-profile {
    display: none;
    position: absolute;
    right: 0;
    z-index: 10;
    margin-top: 16px;
    width: 164px;
    border-radius: 4px;
    background-color: #ffffff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* shadow-lg */
    border: 1px solid rgba(0, 0, 0, 0.05); /* ring-black ring-opacity-5 */
    outline: none;
}

/* Dropdown item styling */
.dropdown-item {
    display: block;
    padding: 8px 16px;
    font-size: 0.875rem;
    color: #4b5563; /* gray-700 */
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f3f4f6; /* bg-gray-100 */
    color: #1f2937; /* text-gray-900 */
}
</style>
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
        <div class="menu-container">
            <div>
                <button type="button" class="menu-button" id="menu-button" aria-expanded="true" aria-haspopup="true">
                    <?php echo 'Logged in as '.$result['first_name'].' '.$result['last_name']; ?>
                    <svg class="menu-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="dropdown-profile" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                <div role="none">
                    <a href="#" onclick="profile();" class="dropdown-item" role="menuitem" tabindex="-1" id="menu-item-0">Account settings</a>
                    <a href="#" onclick="logout();" class="dropdown-item" role="menuitem" tabindex="-1" id="menu-item-1">Sign out</a>
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
document.getElementById("menu-button").addEventListener("click", function () {
    const dropdown = document.querySelector(".dropdown-profile");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
});
</script>
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