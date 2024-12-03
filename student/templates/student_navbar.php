<?php
$db = new Database();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.css">
<style type="text/css">
    .menu-container {
    position: relative;
    display: inline-block;
    text-align: left;
}

/* Button styling */
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
@media (max-width: 582px) {
    .meta-header {
        padding: 0;
    }
    .logo-home {
        margin-left: 5px;
    }
    .user-name {
        display: none;
    }
    #menu-button {
        padding: .5rem;
    }
    .title-system {
        display: none;
    }
    .logo-w-name {
        margin: 0;
    }
    .search-bar {
        margin: 0 !important;
    }
    .inbox-container {
        width: 100%;
    }
    .left-side-nav {
        padding-inline: .875rem;
    }
    .advance-filter-search p {
        margin-bottom: 0 !important;
    }
    .advance-filter-search label {
        margin-bottom: .445rem;
    }
    .login-btn {
        padding: .545rem 1.165rem;
    }
    
}
</style>
<?php 
if (isset($_SESSION['auth_user']['student_id'])){
    echo '
    <div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
        <div class="nano">
            <div class="nano-content">
                <ul class="navbar">
                    <li><a href="project_list.php"><img src="../../images/archive.svg" style="width: 2.225rem; height: 2.225rem;">Archive</a></li>
                    <li><a href="all_project_list.php"><img src="../../images/documents.svg" style="width: 2.225rem; height: 2.225rem;">Research</a></li>
                </ul>
            </div>
        </div>
    </div>';
}
?>

    <!-- /# header -->
    <div class="header">
        <div class="meta-header">
            <div class="w-100 justify-content-between align-items-center" style="display: flex;">
                <div class="logo-home" style="flex:1">
                    <?php 
                        if (isset($_SESSION['auth_user']['student_id'])){
                            echo '
                            <div class="hamburger sidebar-toggle">
                                <span class="line"></span>
                                <span class="line"></span>
                                <span class="line"></span>
                            </div>';
                        }
                    ?>
                    <a href="all_project_list.php">
                        <div class="logo-w-name">
                            <div class="logo-img">
                                <img class="logo-header" src="../images/logo2.webp" alt="">
                            </div>
                            <div class="logo-name">
                                <p class="title-system">Archiver</p>
                            </div>
                        </div>
                    </a>
                </div>
                    <div class="left-side-nav" style="display: flex; flex-direction: row; justify-content:end; align-items: center; flex:1">
                        <div class="search-bar flex-1" style="padding: 0.675rem;">
                            <input id="searchInput" name="searchInput" type="text" class="form-control" style="font-size: 12px; height: auto" placeholder="Search...">
                            <button class="search-btn" id="search-btn" style="background-color: transparent">
                                <img style="width: 1.275rem; height: 1.275rem; " src="../../images/search.svg" alt="">
                            </button>
                        </div>
                        <?php
                            if(isset($_SESSION['auth_user']['student_id'])) {
                            $studentID = $_SESSION['auth_user']['student_id'];
                        ?>
                        <div class="dropdown dib">
                            <div class="header-icon" style="padding-left: 0px !important" data-toggle="dropdown">
                                <img class="" src="../../images/notification-bell.svg" style="position: relative; width: 1.525rem; height: 1.525rem">
                                    <span class="" style="position: absolute; right: 10px; top: 5px; border-radius: 50%; font-size:8px; background-color: #a33333; padding: 5px; color: white" id="notification-badge">
                                    <?php
                                        $unread = 'Unread';
                                        $total_unread = $db->studentNOTIFICATION_COUNT($studentID, $unread);

                                        echo $total_unread;
                                    ?>
                                    </span>
                                </img>
                                <div class="drop-down dropdown-menu dropdown-menu-right" style="position: absolute; transform: translate3d(-227px, -3px, 0px); top: 0px; left: 0px; will-change: transform; max-height: 300px; overflow: auto; border: 1px solid #ccc;">
                                    <div class="dropdown-content-heading">
                                        <span class="text-left" style="font-size: 11px">Recent Notifications</span>
                                        <span class="text-center">
                                                <a href="#" style="font-size: 10px" class="more-link" id="markASread">Mark all as read</a>
                                        </span>
                                    </div>
                                    <div class="dropdown-content-body">
                                        <ul>
                                        
                                        <?php
                                            $notifications = $db->studentNOTIFICATION_Read_Unread($studentID);
                                            foreach ($notifications as $notification) {
                                        ?>
                                                <li>
                                                    <a href="#">
                                                        <img class="pull-left m-r-10 avatar-img" src="<?php echo $notification['profile_picture']; ?>" alt="" />
                                                        <div class="notification-content">
                                                            <small class="notification-timestamp pull-right"><?php echo $notification['logs_time']; ?></small>
                                                            <div class="notification-heading"><?php echo $notification['logs']; ?></div>
                                                            <div class="notification-text"><?php echo $notification['logs_date']; ?> <div class="notification-timestamp pull-right" id="unreadTORead"><?php echo $notification['status']; ?></div> </div>
                                                            
                                                        </div>
                                                    </a>
                                                </li>
                                        <?php
                                            }
                                        ?>

                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            if(isset($_SESSION['auth_user']['student_id'])) {
                                $student_id = $_SESSION['auth_user']['student_id'];

                                
                                $result = $db->student_profile($student_id);

                            }  
                        ?>
                        <div class="menu-container">
                            <div>
                                <a href="#" type="button" style="text-wrap: nowrap" class="menu-button" id="menu-button" aria-expanded="true" aria-haspopup="true">
                                    <span class="user-name"><?php echo $result['first_name']; ?> <?php echo $result['last_name']; ?></span>
                                    <svg class="menu-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                            <div class="dropdown-profile" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                <div role="none">
                                    <a href="#" onclick="profile();" class="dropdown-item" role="menuitem" tabindex="-1" id="menu-item-0">Account settings</a>
                                    <a href="#" onclick="logout();" class="dropdown-item" role="menuitem" tabindex="-1" id="menu-item-1">Sign out</a>
                                </div>
                            </div>
                        </div>
                        <?php
                        } else {
                           echo '<a class="login-btn" href="login.php">Log in</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById("menu-button").addEventListener("click", function () {
        const dropdown = document.querySelector(".dropdown-profile");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
</script>
<script>
    $(document).ready(function() {
        $("#markASread").on("click", function() {
            $.ajax({
                url: "mark_student_notifications_as_read.php", 
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        notificationCount = 0;
                        notificationRead = 'Read';
                        $("#notification-badge").text(notificationCount);
                        $("#unreadTORead").text(notificationRead);
                    } 
                },
                error: function() {
                    alert("An error occurred while processing the request");
                }
            });
        });
    });
    function filteredData() {
    var searchInput = $('#searchInput').val();

    var url = 'all_project_list.php?searchInput=' + encodeURIComponent(searchInput)

    window.location.href = url;
    }

    $('#search-btn').on('click', function() {
    filteredData();
    });
</script>


<script>
    function my_research() {
    window.location.href ='project_list.php';
  }
  function profile() {
    window.location.href = 'student_profile.php?menuTab=accountInfo';
  }
  function logout() {
    window.location.href = 'student_logout.php ';
  }
</script>
