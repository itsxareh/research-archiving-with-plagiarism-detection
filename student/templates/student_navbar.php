<?php
$db = new Database();
?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.9.3/tagify.css">

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
    <div class="nano">
        <div class="nano-content">
            <ul class="navbar">
                <li><a href="project_list.php"><i class="ti-plus"></i> Create</a></li>
                <li><a href="all_project_list.php"><i class="ti-folder"></i> Research </a></li>
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
                    <a href="all_project_list.php">
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
                        <div class="search-bar m-r-16">
                            <input id="searchInput" name="searchInput" type="text" class="form-control" placeholder="Search...">
                            <button class="search-btn" id="search-btn"><i class="ti-search"></i></button>
                        </div>
                        <?php
                            if(isset($_SESSION['auth_user']['student_id'])) {
                            $studentID = $_SESSION['auth_user']['student_id'];
                        ?>
                        <div class="dropdown dib">
                            <div class="header-icon" style="padding: 5px 15px 0 15px !important" data-toggle="dropdown">
                                <i class="ti-bell" style="position: relative;">
                                    <span class="" style="position: absolute; right: -12px; top: -8px; border-radius: 50%; font-size: 12px; background-color: #a33333; padding: 5px; color: white" id="notification-badge">
                                    <?php
                                        $unread = 'Unread';
                                        $total_unread = $db->studentNOTIFICATION_COUNT($studentID, $unread);

                                        echo $total_unread;
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
                        
                        <div class="dropdown dib">
                            <div class="header-icon" data-toggle="dropdown">
                            <?php
                                if(isset($_SESSION['auth_user']['student_id'])) {
                                    $student_id = $_SESSION['auth_user']['student_id'];


                                    $result = $db->student_profile($student_id);

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
                           echo '<a class="item-meta" href="login.php" style="padding: 10px 32px;">Login</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#markASread").on("click", function() {
            $.ajax({
                url: "mark_student_notifications_as_read.php", 
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // Update the notification count
                        notificationCount = 0;
                        notificationRead = 'Read';
                        $("#notification-badge").text(notificationCount);
                        $("#unreadTORead").text(notificationRead);

                        alert("Notifications marked as read");
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
