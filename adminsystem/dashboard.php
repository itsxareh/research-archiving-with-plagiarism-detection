<?php

include '../connection/config.php';
$db = new Database();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
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
if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    exit(); 
    
} elseif(!hasPermit($permissions, 'dashboard_view')) {
    header('Location:../../bad-request.php');
    exit(); 
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- theme meta -->
    <meta name="theme-name" content="focus" />
    <title>Admin Dashboard: Research Archiving System</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo2.webp">
    <!-- Retina iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <!-- Retina iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <!-- Standard iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <!-- Standard iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">
    <!-- Styles -->
    <link href="css/lib/calendar2/pignose.calendar.min.css" rel="stylesheet">
    <link href="css/lib/chartist/chartist.min.css" rel="stylesheet">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/weather-icons.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="../css/action-dropdown.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->


    <div class="content-wrap">
            <div class="main container-fluid">
                <div class="col-md-12 title-page">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Hello, Admin</h1>
                        </div>
                    </div>
                </div>
                <!-- /# row -->
                <section class="col-md-12 col-xl-12">
                    <div class="b-row">

                        <div class="col-md-12 col-xl-8">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="flex justify-content-between">
                                        <h4 class="card-title mb-3">Published Research/Month</h4>
                                        <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                        <div class="action-container">
                                            <div>
                                                <button type="button" class="action-button"  id="action-button_published-research" aria-expanded="true" aria-haspopup="true">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_published-research" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                <div role="none">
                                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_published_research"  class="dropdown-action-item">
                                                        Generate PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="publishedResearchPerMonthChart" width="400" height="400"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-4">
                            <div class="b-row nested-row h-100">
                            <div class="col-md-6 col-xl-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="avatar flex-shrink-0 mb-4" style="width:50px; height:50px">
                                                <img src="../adminsystem/images/paper.png" alt="paper">
                                            </div>
                                            <p class="card-title mb-3">Research Papers</p>
                                            <div class="card-text">
                                            <?php
                                                $rows = $db->SELECT_COUNT_ALL_ARCHIVE_RESEARCH();
                                                
                                                echo "<h3>{$rows}</h3>";
                                            ?>
                                            </div>
                                            <p class="mb-0" style="font-size: 14px">Total</p>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <p class="card-title mb-3">Published Research</p>
                                            <div class="card-text">
                                            <?php
                                                $row = $db->SELECT_COUNT_ALL_RESEARCH();
                                                $rows = $db->SELECT_COUNT_ALL_PUBLISHED_RESEARCH();
                                            
                                                echo "<h3 class='mb-4'>{$rows['count']}</h3>";
                                            ?>
                                            </div>
                                            <div class="avatar flex-shrink-0 mb-2" style="width:50px; height:50px">
                                                <img src="../adminsystem/images/publishing.png" alt="publish">
                                            </div>
                                            <?php 
                                                
                                                $published_percentage = ($rows['count'] / $row['count']) * 100;
                                                echo '<p class="mb-0" style="font-size: 14px">'.round($published_percentage, 1).'%</p>';  
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xl-12" >
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="flex justify-content-between">
                                                <h4 class="card-title mb-3">Research Papers/Department</h4>
                                                <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                                <div class="action-container">
                                                    <div>
                                                        <button type="button" class="action-button"  id="action-button_research-paper-per-dept" aria-expanded="true" aria-haspopup="true">
                                                            <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_research-paper-per-dept" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                        <div role="none">
                                                            <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_research_paper_per_dept"  class="dropdown-action-item">
                                                                Generate PDF
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-text" style="white-space: nowrap; overflow-x: auto;">
                                            <?php
                                                $rows = $db->Archive_Research_BasedOn_Department();
                                                $data = json_encode($rows);
                                                
                                                if (is_array($rows) || is_object($rows)) {
                                                    foreach ($rows as $row) {
                                                        echo "<div class='b-row justify-content-between '>
                                                                <p class='no-wrap b-text-ellipsis p-0' style='font-size: 12px; color: #666'>{$row['name']} ({$row['dept_code']})</p>
                                                                <strong>{$row['count']}</strong>
                                                            </div>";
                                                    }
                                                } else {
                                                    echo "<p>No data available.</p>";
                                                }
                                            ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="b-row">
                        <div class="col-md-12 col-xl-6">
                            <div class="card h-100">
                                <div class="card-body" style="overflow-x: hidden;">
                                    <div class="flex justify-content-between">
                                        <div class="flex align-items-center justify-content-between mb-3" style="gap: 15px">
                                            <h4 class="card-title ">Most Viewed Research Paper</h4>
                                            <div class="avatar flex-shrink-0" style="width:50px; height:50px">
                                                <img src="../adminsystem/images/top.png" alt="publish">
                                            </div>
                                        </div>
                                        <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                        <div class="action-container">
                                            <div>
                                                <button type="button" class="action-button"  id="action-button_most-viewed-paper" aria-expanded="true" aria-haspopup="true">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_most-viewed-paper" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                <div role="none">
                                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=most_viewed_paper"  class="dropdown-action-item">
                                                        Generate PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-text">
                                        <div class="chart-container" style="position: relative; height:400px; width:100%;">
                                            <canvas id="topViewsChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="flex justify-content-between">
                                        <h4 class="card-title mb-3">Research Views/Department</h4>
                                        <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                            <div class="action-container">
                                                <div>
                                                    <button type="button" class="action-button"  id="action-button_research-views-per-dept" aria-expanded="true" aria-haspopup="true">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_research-views-per-dept" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                    <div role="none">
                                                        <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=research_views_per_dept"  class="dropdown-action-item">
                                                            Generate PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="chart-container">
                                    <canvas id="viewsPerDepartmentChart" width="400" height="500"></canvas>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="b-row">
                        <div class="col-md-12 col-xl-4">
                                <!-- <div class="col-md-6 col-xl-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <i class="float-right ti-angle-double-right"></i>
                                            <div class="avatar flex-shrink-0 mb-2" style="width:50px; height:50px">
                                                <img src="../adminsystem/images/file.png" alt="unpublish">
                                            </div>
                                            <div class="card-text mb-4">
                                            <?php 
                                                $row = $db->SELECT_COUNT_ALL_UNPUBLISHED_RESEARCH();
                                                echo '<h1>'.$row['count'].' paper/s</h1>';  
                                                
                                            ?>
                                            </div>
                                            <h4 class="card-title mb-3">Unpublished Research</h4>
                                            <?php 
                                                $rows = $db->SELECT_COUNT_ALL_PUBLISHED_RESEARCH();
                                                $unpublished_percentage = ($row['count'] / $rows['count']) * 100;
                                                echo '<p class="mb-0" style="font-size: 18px">'.round($unpublished_percentage, 1).'%</p>';  
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xl-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <i class="float-right ti-angle-double-right"></i>
                                            <h4 class="card-title mb-3">Plagiarized Research</h4>
                                            <div class="card-text mb-4">
                                            <?php 
                                                $rows = $db->SELECT_COUNT_ALL_PLAGIARIZED_RESEARCH();
                                                echo '<h1>'.$rows['count'].' paper/s</h1>';  
                                            ?>
                                            </div>
                                            <div class="avatar flex-shrink-0" style="width:50px; height:50px">
                                                    <img src="../adminsystem/images/plagiarism.png" alt="plagiarism">
                                                </div>
                                        </div>
                                    </div>
                                </div> -->
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="card-text">
                                        <div class="card-list-container">
                                            <div class="flex justify-content-between">
                                                <h4 class="card-title mb-3">Recent Published Research</h4>
                                                <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                                <div class="action-container">
                                                    <div>
                                                        <button type="button" class="action-button"  id="action-button_recent-published-paper" aria-expanded="true" aria-haspopup="true">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                        </svg>
                                                        </button>
                                                    </div>
                                                    <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_recent-published-paper" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                        <div role="none">
                                                            <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=recent_published_paper"  class="dropdown-action-item">
                                                                Generate PDF
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="card-text mt-4">
                                                <ul>
                                                <?php 
                                                    $rows = $db->SELECT_RECENT_RESEARCH_PAPER();
                                                    foreach ($rows as $row) {
                                                        echo '
                                                        <li>
                                                            <p class="mb-0 badge badge-danger" style="font-size: 12px;">'.$row['aid'].'</p>
                                                            <p class="mb-0"><a href="view_archive_research.php?archiveID='.$row['aid'].'" style="color: #333; font-size: 14px; font-weight:700">'.$row['project_title'].'</a></p>
                                                            <p class="" style=" font-size: 12px;">'.DateTime::createFromFormat("Y-m-d", $row['date_published'])->format("d F Y").'</p>
                                                        </li>

                                                        
                                                        ';
                                                    }
                                                ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4 autoShow">
                            <div class="card h-100">
                                <h4 class="card-title mb-3">Departments</h4>
                                <div class="chart-container">
                                    <canvas id="departmentStatusChart" width="300" height="440"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4 autoShow">
                            <div class="card h-100">
                                <h4 class="card-title mb-3">Courses</h4>
                                <div class="chart-container">
                                    <canvas id="courseStatusChart" width="300" height="440"></canvas>
                                </div>  
                            </div>
                        </div>
                    </div>
                    <div class="b-row">
                        <div class="col-md-12 col-xl-8">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="flex justify-content-between">
                                    <h4 class="card-title mb-3">Plagiarized Research Content </h4>
                                        <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                        <div class="action-container">
                                            <div>
                                                <button type="button" class="action-button"  id="action-button_plagiarized-content" aria-expanded="true" aria-haspopup="true">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_plagiarized-content" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                <div role="none">
                                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=plagiarized_content"  class="dropdown-action-item">
                                                        Generate PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-text">
                                        <ul>
                                        <?php 
                                            $rows = $db->SELECT_PLAGIARIZED_RESEARCH_CONTENT();
                                            if (!empty($rows)){
                                                foreach ($rows as $row) {
                                                    $percentage = $row['total_percentage'];
                                                    if ($percentage > 100){
                                                        $percentage = 100;
                                                    }
                                                    echo '
                                                    <li class="flex justify-content-between">
                                                        <div class="w-100">
                                                            <p class="mb-0"><a href="plagiarism_result.php?archiveID='.$row['aid'].'" style="color: #333; font-size: 14px; font-weight:700">'.$row['project_title'].'</a></p>
                                                            <p class="" style=" font-size: 12px;">'.(new DateTime($row['dateOFSubmit']))->format("d F Y h:i:s A").'</p>
                                                        </div>
                                                        <div class="d-flex align-items-center w-100">
                                                            <div class="progress w-100" style="height: 10px">
                                                                <div class="progress-bar-danger progress-bar" role="progressbar" style="width: '.round($percentage, 1).'%" aria-valuenow="'.round($percentage, 1).'" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                            <span style="color: #a33333; font-size: 16px; margin-left: .75rem !important;">'.round($percentage, 1).'%</span>
                                                        </div>
                                                    </li>
    
                                                    
                                                    ';
                                                }
                                            } else {
                                                 echo '<p class="text-center" style="color: #666; font-size: 12px;" >No plagiarized research content found.</p>';
 
                                            }
                                        ?>
                                        </ul>                   
                                    </div>
                                </div>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xl-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="flex justify-content-between">
                                        <h4 class="card-title mb-3">Top Contributor</h4>
                                        <?php if (hasPermission($permissions, 'dashboard_download')): ?>
                                        <div class="action-container">
                                            <div>
                                                <button type="button" class="action-button"  id="action-button_top-contributor" aria-expanded="true" aria-haspopup="true">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="#000000" stroke-width="1" stroke-linecap="round" stroke-linejoin="miter"><line x1="5.99" y1="12" x2="6" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="11.99" y1="12" x2="12" y2="12" stroke-linecap="round" stroke-width="2"></line><line x1="17.99" y1="12" x2="18" y2="12" stroke-linecap="round" stroke-width="2"></line>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="dropdown-action" style="width: 120px; line-height: 24px;" id="dropdown_top-contributor" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                <div role="none">
                                                    <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=top_contributor"  class="dropdown-action-item">
                                                        Generate PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-text" style="overflow-x: auto;">
                                    <table class="table list-table w-100">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email Address</th>
                                                <th>Published Research</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rows = $db->SELECT_TOP_RESEARCH_CONTRIBUTOR();
                                            $data = json_encode($rows);
                                            
                                            if (is_array($rows) || is_object($rows)) {
                                                foreach ($rows as $row) {
                                                    if ($row['first_name'] == '') {
                                                         $row['first_name'] = '-';
 
                                                    }
                                                    echo "<tr>
                                                            <td class='list-td'>{$row['first_name']} {$row['middle_name']} {$row['last_name']}</td>
                                                            <td class='list-td'><a href='view_profile.php?studID={$row['studID']}'> {$row['research_owner_email']}<i class='ti-arrow-top-right'></i></a></td>
                                                            <td class='list-td'>{$row['count']}</td>
                                                        </tr>";
                                                }
                                            } else {
                                                echo "<p class='text-center' style='color: #333; font-size: 14px; font-weight:700'>No data available.</p>";
                                            }
                                            
                                            ?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>  
                        </div>                                       
                    </div>
                    <?php include 'templates/footer.php'; ?>
            </div>
<!-- <script src="js/lib/calendar-2/moment.latest.min.js"></script>
<script src="js/lib/calendar-2/pignose.calendar.min.js"></script>
<script src="js/lib/calendar-2/pignose.init.js"></script> -->
<script>
<?php 
    $result = $db->Archive_Research_Views_BasedOn_Departments();
    $data = json_encode($result);
?>

const departmentViewsData = <?php echo $data; ?>;
    const departmentLabels = departmentViewsData.map(item => item.name);
    const viewCounts = departmentViewsData.map(item => item.count);

    const departmentColors = departmentLabels.map((_, index) => {
        const colors = [
            'rgba(255, 99, 132, 0.6)', // Red
            'rgba(54, 162, 235, 0.6)', // Blue
            'rgba(255, 206, 86, 0.6)', // Yellow
            'rgba(75, 192, 192, 0.6)', // Green
            'rgba(153, 102, 255, 0.6)', // Purple
            'rgba(255, 159, 64, 0.6)',  // Orange
            'rgba(201, 203, 207, 0.6)'  // Grey
        ];
        return colors[index % colors.length];
    });

    const ctx1 = document.getElementById('viewsPerDepartmentChart').getContext('2d');

    const createViewsChart = () => {
        const hideXAxisLabels = window.innerWidth <= 567;

        return new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: departmentLabels,
                datasets: [{
                    label: 'Research Views',
                    data: viewCounts,
                    backgroundColor: departmentColors,
                    borderColor: departmentColors.map(color => color.replace('0.6', '1')), // Make border color solid
                    borderWidth: 1,
                    hoverBackgroundColor: '#A33333',
                    hoverBorderColor: '#333333',
                    hoverBorderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        display: !hideXAxisLabels,
                        grid: { display: false },
                        ticks: { color: '#333', font: { size: 12, weight: 'bold' } }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(200, 200, 200, 0.3)' },
                        ticks: { color: '#666', font: { size: 12, weight: 'bold' } }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: '#333', font: { size: 14, weight: 'bold' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        displayColors: false
                    }
                }
            }
        });
    };

    let viewsPerDepartmentChart = createViewsChart();

    // Recreate the chart on window resize to handle dynamic updates
    window.addEventListener('resize', () => {
        if (viewsPerDepartmentChart) {
            viewsPerDepartmentChart.destroy();
        }
        viewsPerDepartmentChart = createViewsChart(); 
    });

<?php
    $rows = $db->SELECT_RESEARCH_PUBLISHED_PER_WEEK();
    $data = json_encode($rows);
?>
    const dataFromPHP = <?php echo $data; ?>;
    const labels = dataFromPHP.map(item => item.date_published); 
    const values = dataFromPHP.map(item => item.count); 

    const ctx = document.getElementById('publishedResearchPerMonthChart').getContext('2d');
    const publishedResearchChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Research Published Per Month',
                data: values,
                borderColor: '#3a7bd5',
                backgroundColor: 'rgba(58, 123, 213, 0.2)',
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#3a7bd5',
                pointBorderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#999999',
                        font: {
                            family: 'Segoe UI',
                            size: 12,
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.2)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666666',
                        font: {
                            family: 'Segoe UI',
                            size: 12,
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#333333',
                        font: {
                            family: 'Segoe UI',
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#3a7bd5',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false 
                }
            }
        }
    });
<?php 
    $totalCourses = $db->SELECT_COUNT_ALL_COURSES();
    $activeCourses = $db->SELECT_COUNT_ALL_ACTIVE_COURSES();

    $activePercentage = $activeCourses['count'] ;
    $inactivePercentage = $totalCourses['total_count'] - $activePercentage;
?>
    const ctx2 = document.getElementById('courseStatusChart').getContext('2d');
    const courseStatusChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                label: 'Course Status',
                data: [<?php echo $activePercentage; ?>, <?php echo $inactivePercentage; ?>],
                backgroundColor: ['rgba(153, 102, 255, 0.6)', 'rgba(255, 206, 86, 0.6)'],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverBackgroundColor: '#A33333',
                hoverBorderColor: '#333333',
                hoverBorderWidth: 1,
                barThickness: 30,
                maxBarThickness: 50,
                minBarLength: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold',
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    bodyColor: '#ffffff',
                    borderColor: '#4CAF50',
                    borderWidth: 1,
                }
            }
        }
    });
    <?php 
    $totalDepartment = $db->SELECT_COUNT_ALL_DEPARTMENT();
    $activeDepartment = $db->SELECT_COUNT_ALL_ACTIVE_DEPARTMENT();

    $activePercentage = $activeDepartment['count'] ;
    $inactivePercentage = $totalDepartment['total_count'] - $activePercentage;
?>
    const ctx3 = document.getElementById('departmentStatusChart').getContext('2d');
    const departmentStatusChart = new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive'],
            datasets: [{
                label: 'Course Status',
                data: [<?php echo $activePercentage; ?>, <?php echo $inactivePercentage; ?>],
                backgroundColor: ['rgba(0, 104, 22, 0.4)', 'rgba(209, 102, 255, 0.6)'],
                borderWidth: 2,
                hoverBackgroundColor: '#A33333',
                hoverBorderColor: '#333333',
                hoverBorderWidth: 1,
                barThickness: 30,
                maxBarThickness: 50,
                minBarLength: 5,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        color: '#333',
                        font: {
                            size: 14,
                            weight: 'bold',
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    bodyColor: '#ffffff',
                    borderColor: '#4CAF50',
                    borderWidth: 1,
                }
            },
            doughnutLabel: {
                labels: [
                    {
                        text: 'Active/Inactives',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                ]
            }
        },
        cutout: '70%',
    });    

<?php
    $rows = $db->SELECT_TOP_10_VIEWS_RESEARCH_PAPER();
    $data = json_encode($rows);
?>

    const dataFromPHP1 = <?php echo $data; ?>;
    const labels1 = dataFromPHP1.map(item => item.project_title);
    const values1 = dataFromPHP1.map(item => item.count);
    const backgroundColors = [
        '#42A5F5', '#66BB6A', '#FFA726', '#AB47BC', '#EC407A', 
        '#FF7043', '#26C6DA', '#FFCA28', '#8D6E63', '#78909C'
    ];
    const ctx4 = document.getElementById('topViewsChart').getContext('2d');

    const createChart = () => {
        const hideYAxisLabels = window.innerWidth <= 567;

        return new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: labels1,
                datasets: [{
                    label: 'Views',
                    data: values1,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1,
                    hoverBackgroundColor: '#A33333',
                    hoverBorderColor: '#333333',
                    hoverBorderWidth: 1,
                    barThickness: 30,
                    maxBarThickness: 50,
                    minBarLength: 5,
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar chart
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            color: '#666',
                            font: {
                                family: 'Segoe UI',
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        display: !hideYAxisLabels, // Hide Y-axis if max-width <= 567
                        ticks: {
                            color: '#333',
                            font: {
                                family: 'Segoe UI',
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#333',
                            font: {
                                family: 'Segoe UI',
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#1E88E5',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    };

    // Create the chart
    let myChart = createChart();

    window.addEventListener('resize', () => {
        if (myChart) {
            myChart.destroy();
        }
        myChart = createChart();
    });
</script>
<script>
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
