<?php
$db = new Database();
?>


<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
    <div class="nano">
        <div class="nano-content">
            <ul class="navbar">
                <li><a href="dashboard.php"><i class="ti-home"></i> Home</a></li>
                <li><a href="archive_list.php"><i class="ti-folder"></i> Research </a></li>
                <li><a href="student_list.php"><i class="ti-layout-list-thumb"></i><span>Student</span></a></li>
                <li><a href="department_list.php"><i class="ti-agenda"></i><span>Department</span></a></li>
                <li><a href="course_list.php"><i class="ti-layout-menu-v"></i><span>Course</span></a></li>
                <li><a href="admin_list.php"><i class="ti-user"></i><span>Admin</span></a></li>
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
                <a href="dashboard.php">
                    <div class="logo-w-name">
                        <div class="logo-img">
                            <img class="logo-header" src="../images/logo2.png" alt="">
                        </div>
                        <div class="logo-name">
                            <p class="title-system">Archiver</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="" style="display: flex; flex-direction: row; align-items: center">
                <div class="dropdown dib">
                    <div class="header-icon" data-toggle="dropdown">
                        <i class="ti-bell">
                            <span class="badge badge-danger" style="border-radius: 50%;" id="notification-badge">
                            <?php
                                if(isset($_SESSION['auth_user']['admin_id'])) {
                                    
                                    $adminID = $_SESSION['auth_user']['admin_id'];
                                    $unread = 'Unread';

                                    // Adjust your SQL query based on your database schema
                                    $total_unread = $db->adminsystemNOTIFICATION_COUNT($adminID, $unread);

                                    echo $total_unread;
                                }
                            ?>
                            </span>
                        </i>
                        <div class="drop-down dropdown-menu dropdown-menu-right" style="position: absolute; transform: translate3d(-227px, -3px, 0px); top: 0px; left: 0px; will-change: transform; height: 300px; overflow: auto; border: 1px solid #ccc;">
                            <div class="dropdown-content-heading">
                                <span class="text-left">Recent Notifications</span>
                            </div>
                            <div class="dropdown-content-body">
                                <ul>
                                <li class="text-center">
                                        <a href="#" class="more-link" id="markASread"><i class="ti-email"></i> Mark all as read</a>
                                </li>
                                <?php
                                if(isset($_SESSION['auth_user']['admin_id'])) {
                                    $coordinatorID = $_SESSION['auth_user']['admin_id'];
                                    
                                    // Adjust your SQL query based on your database schema
                                    $notifications = $db->adminsystemNOTIFICATION_Read_Unread($coordinatorID);
                                    
                                    foreach ($notifications as $notification) {
                                ?>
                                        <li>
                                            <a href="#">
                                                <img class="pull-left m-r-10 avatar-img" src="<?php echo $notification['admin_profile_picture']; ?>" alt="" />
                                                <div class="notification-content">
                                                    <small class="notification-timestamp pull-right"><?php echo $notification['logs_time']; ?></small>
                                                    <div class="notification-heading"><?php echo $notification['logs']; ?></div>
                                                    <div class="notification-text"><?php echo $notification['logs_date']; ?> <div class="notification-timestamp pull-right" id="unreadTORead"><?php echo $notification['status']; ?></div> </div>
                                                    
                                                </div>
                                            </a>
                                        </li>
                                <?php
                                    }
                                }
                                ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown dib">
                    <div class="header-icon" data-toggle="dropdown">
                    <?php
                        if(isset($_SESSION['auth_user']['admin_id'])) {
                            $adminID = $_SESSION['auth_user']['admin_id'];


                            $result = $db->admin_profile($adminID);

                        }
                    ?>
                        <span class="user-avatar"><?php echo $result['first_name'];?> <?php echo $result['last_name'];?>
                            <i class="ti-angle-down f-s-10"></i>
                        </span>

                        <div class="drop-down dropdown-profile dropdown-menu dropdown-menu-right">
                            <div class="dropdown-content-body">
                                <ul>
                                    <li>
                                        <a href="#" onclick="profile();">
                                            <i class="ti-settings"></i>
                                            <span>Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="add_admin();">
                                            <i class="ti-user"></i>
                                            <span>Add Admin</span>
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
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Attach a click event handler to the bell icon
        $("#markASread").on("click", function() {
            // Send an AJAX request to mark notifications as read
            $.ajax({
                url: "mark_admin_notifications_as_read.php", // Replace with the correct URL
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Update the notification count
                        notificationCount = 0;
                        notificationRead = 'Read';
                        $("#notification-badge").text(notificationCount);
                        $("#unreadTORead").text(notificationRead);

                        // Update the UI or reload notifications here
                        // You can update the notifications without reloading the page here
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
