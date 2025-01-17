<?php
include 'connection/config.php';
$db = new Database();
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EARIST Repository</title>
    <link rel="shortcut icon" href="../adminsystem/images/logo2.webp">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
     <link rel="stylesheet" href="../css/index.css">
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
  
  <!-- Index Content -->
    <div class="header">
        <div class="nav-header">
        <div class="logo">
            <a href="index.php"><img src="images/logo2.webp"></a>
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
    <div class="content-wrapper h-100">
      <div class="col-sm-12 col-md-12 col-xl-12" style="color: #000">
      <div class="row" style="max-width: 800px; background-color: #f8f9fa; padding: 20px; border-radius: 10px;">
            <div class="col-12">
                <h4 class="text-center mb-4">About Us</h4>
                
                <div class="card shadow-sm mb-5" style="background-color: transparent; border: none">
                    <div class="card-body" >
                        <h5 class="card-title text-center">EARIST Repository</h5>
                        <p class="card-text text-center">
                            The EARIST Repository is a comprehensive digital repository designed to preserve, 
                            manage, and share research papers and academic works from the EARIST community. Our platform 
                            serves as a central hub for students, faculty, and researchers to access valuable academic resources.
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm mb-5" style="background-color: transparent; border: none">
                    <div class="card-body" >
                        <h5 class="card-title text-center">Our Mission</h5>
                        <p class="card-text text-center">
                            To facilitate the preservation and dissemination of academic research while promoting scholarly 
                            communication and collaboration within the EARIST community and beyond.
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm mb-5" style="background-color: transparent; border: none">
                    <div class="card-body" >
                        <h5 class="card-title text-center">Key Features</h5>
                        <ul class="card-text text-center">
                            <li>Digital preservation of research papers and academic works</li>
                            <li>Easy access to scholarly resources</li>
                            <li>Secure and organized research management</li>
                            <li>Platform for academic collaboration</li>
                            <li>Support for various research disciplines</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm" style="background-color: transparent; border: none">
                    <div class="card-body" >
                        <h5 class="card-title text-center">Contact Information</h5>
                        <p class="card-text text-center">
                            For inquiries and support, please contact:<br>
                            Email: research@earist.edu.ph<br>
                            Address: Nagtahan, Sampaloc, Manila, Philippines<br>
                            Phone: (02) 8286-0677
                        </p>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
<?php include 'student/templates/footer.php'; ?>
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