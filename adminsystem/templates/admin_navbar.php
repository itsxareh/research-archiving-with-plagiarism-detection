<?php
$db = new Database();
?>
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
    align-items: center;
    gap: 2px; /* Adjust spacing as needed */
    border-radius: 4px;
    background-color: #ffffff;
    padding: 8px 12px;
    font-size: 1.025rem;
    font-weight: 600;
    color: #1f2937;
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
    #menu-button {
        text-indent: -9999px;
    }
    .title-system {
        display: none;
    }
    .logo-w-name {
        margin: 0;
    }
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.min.js"></script>
<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
    <div class="nano">
        <div class="nano-content">
            <?php
            // Get user's role and permissions
            $userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
            $permissions = explode(',', $userRole['permissions']);

            // Helper function to check permissions
            function hasPermission($permissions, $permissionToCheck) {
                foreach ($permissions as $permission) {
                    if (strpos($permission, $permissionToCheck) === 0) {
                        return true;
                    }
                }
                return false;
            }
            ?>

            <ul class="navbar" style="height: <?= hasPermission($permissions, 'role_view') ? '100%' : 'auto' ?>">
                <?php if (hasPermission($permissions, 'dashboard_view')): ?>
                    <li><a href="dashboard.php"><img src="../../images/home.svg" style="width: 2.225rem; height: 2.225rem;">Home</a></li>
                <?php endif; ?>
                
                <?php if (hasPermission($permissions, 'research_view')): ?>
                    <li><a href="research-papers.php"><img src="../../images/documents.svg" style="width: 2.225rem; height: 2.225rem;">Research </a></li>
                <?php endif; ?>

                <?php if (hasPermission($permissions, 'student_list_view')): ?>
                    <li><a href="students.php"><img src="../../images/students.svg" style="width: 2.225rem; height: 2.225rem;"><span>Student</span></a></li>
                <?php endif; ?>

                <?php if (hasPermission($permissions, 'department_view')): ?>
                    <li><a href="departments.php"><img src="../../images/department.svg" style="width: 2.225rem; height: 2.225rem;"><span>Department</span></a></li>
                <?php endif; ?>

                <?php if (hasPermission($permissions, 'course_view')): ?>
                    <li><a href="courses.php"><img src="../../images/course.svg" style="width: 2.225rem; height: 2.225rem;"><span>Course</span></a></li>
                <?php endif; ?>

                <?php if (hasPermission($permissions, 'user_view')): ?>
                    <li><a href="admins.php"><img src="../../images/admin.svg" style="width: 2.225rem; height: 2.225rem;"><span>Admin</span></a></li>
                <?php endif; ?>

                <?php if (hasPermission($permissions, 'role_view')): ?>
                    <li><a href="roles.php"><img src="../../images/role.svg" style="width: 2.225rem; height: 2.225rem;"><span>Role</span></a></li>
                <?php endif; ?>

                
            </ul>
        </div>
    </div>
</div>
    <!-- /# sidebar -->

<div class="header">
    <div class="meta-header">
        <div class="row w-100 justify-content-between align-items-center">
            <div class="logo-home">
                <div class="hamburger sidebar-toggle">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </div>
                <a href="index.php">
                    <div class="logo-w-name">
                        <div class="logo-img">
                            <img class="logo-header" src="../images/logo2.webp" alt="">
                        </div>
                        <div class="logo-name">
                            <p class="title-system">Imbakan ni Amang</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="" style="display: flex; flex-direction: row; align-items: center">
                <?php
                    if(isset($_SESSION['auth_user']['admin_id'])) {
                    
                        $adminID = $_SESSION['auth_user']['admin_id'];
                    }
                ?>
                <div class="dropdown dib">
                    <div class="header-icon" style="padding: 5px 15px 0 15px !important" data-toggle="dropdown">
                        <img class="" src="../../images/notification-bell.svg" style="position: relative; width: 1.525rem; height: 1.525rem">
                            <span class="" style="position: absolute; right: 5px; top: 0; border-radius: 50%; font-size: 10px; background-color: #a33333; padding: 5px; color: white" id="notification-badge">
                            <?php
                                $unread = 'Unread';
                                $total_unread = $db->adminsystemNOTIFICATION_COUNT($adminID, $unread);

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
                                    
                                    $notifications = $db->adminsystemNOTIFICATION_Read_Unread($adminID);
                                    foreach ($notifications as $notification) {
                                ?>
                                        <li>
                                            <a href="#">
                                                <img class="pull-left m-r-10 avatar-img" src="<?= isset($notification['admin_profile_picture']) ? $notification['admin_profile_picture'] : '../../images/default-profile.png' ?>" alt="" />
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
                        if(isset($_SESSION['auth_user']['admin_id'])) {
                            $adminID = $_SESSION['auth_user']['admin_id'];


                            $result = $db->admin_profile($adminID);

                        }
                    ?>
                <div class="menu-container">
                    <div>
                        <button type="button" class="menu-button" id="menu-button" aria-expanded="true" aria-haspopup="true">
                            <?php echo $result['first_name']; ?> <?php echo $result['last_name']; ?>
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
                <!-- <div class="dropdown dib">
                    <div class="header-icon" data-toggle="dropdown">

                        <span class="user-avatar">
                            <i class="ti-angle-down f-s-10"></i>
                        </span>

                        <div class="drop-down dropdown-profile dropdown-menu dropdown-menu-right">
                            <div class="dropdown-content-body">
                                <ul>
                                    <li>
                                        <a href="#" onclick="profile();">
                                            <i class="ti-settings"></i>
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
                </div> -->
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#markASread").on("click", function() {
            $.ajax({
                url: "mark_admin_notifications_as_read.php",
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Update the notification count
                        notificationCount = 0;
                        notificationRead = 'Read';
                        $("#notification-badge").text(notificationCount);
                        $("#unreadTORead").text(notificationRead);

                    } else {
                        alert("Failed to mark notifications as read");
                    }
                },
                error: function() {
                    alert("An error occurred while processing the request");
                }
            });
        });
    });
</script>
<script>
    document.getElementById("menu-button").addEventListener("click", function () {
        const dropdown = document.querySelector(".dropdown-profile");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });
</script>

<script>
  function profile() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_profile.php ';
  }
  function settings() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_settings.php ';
  }
  function add_admin() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_register.php ';
  }
  function logout() {
    // Reload the page with a query parameter to indicate the selected user
    window.location.href = 'admin_logout.php ';
  }
</script>

<script src="js/lib/jquery.min.js"></script>
<script src="js/lib/jquery.nanoscroller.min.js"></script>
<script src="js/lib/menubar/sidebar.js"></script>
<script src="js/lib/preloader/pace.min.js"></script>
<script src="js/lib/bootstrap.min.js"></script>
<script src="js/scripts.js"></script>


<!-- Sweet Alert -->
<script src="js/lib/sweetalert/sweetalert.min.js"></script>
<script src="js/lib/sweetalert/sweetalert.init.js"></script>

