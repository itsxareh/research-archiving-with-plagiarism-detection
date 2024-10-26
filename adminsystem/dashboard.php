<?php

include '../connection/config.php';
$db = new Database();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    
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
    <title>Admin Dashboard: Student Research Archiving Management System</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo1.png">
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
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/weather-icons.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
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
                <section id="main-content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-files color-primary border-primary"></i>
                                    </div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Departments</div>
                                        <?php
                                        $totalDepartments = $db->SELECT_COUNT_ALL_DEPARTMENTS();
                                        $activeDepartments = $db->SELECT_COUNT_ACTIVE_DEPARTMENTS();
                                        $inactiveDepartments = $totalDepartments - $activeDepartments;
                                        
                                        // Calculate the percentage
                                        $activePercentage = ($activeDepartments / $totalDepartments) * 100;
                                        $inactivePercentage = ($inactiveDepartments / $totalDepartments) * 100;

                                        ?>
                                        <div class="stat-digit"><?php echo $totalDepartments ?></div>
                                    </div>
                                    <!-- <canvas id="departmentChart" style="max-height: 300px;"></canvas> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-list color-success border-success"></i>
                                    </div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Courses</div>
                                        <?php
                                        $count = $db->SELECT_COUNT_ALL_COURSES();

                                        ?>
                                        <div class="stat-digit"><?php echo $count ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-user color-primary border-primary"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Students</div>
                                        <?php
                                        $count = $db->SELECT_COUNT_ALL_StudentsData_WHERE_VERIFIED();

                                        ?>
                                        <div class="stat-digit"><?php echo $count ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-user color-danger border-danger"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">For Approval Students</div>
                                        <?php
                                        $count = $db->SELECT_COUNT_ALL_StudentsData_WHERE_NOT_VERIFIED();

                                        ?>
                                        <div class="stat-digit"><?php echo $count ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-archive color-success border-success"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Accepted Research</div>
                                        <?php
                                        $count = $db->SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH();

                                        ?>
                                        <div class="stat-digit"><?php echo $count ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-archive color-danger border-danger"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Not Accepted Research</div>
                                        <?php
                                        $count = $db->SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_UNPUBLISH();

                                        ?>
                                        <div class="stat-digit"><?php echo $count ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-folder color-warning border-warning"></i></div>
                                    <div class="stat-content dib">
                                        <div class="stat-text">Plagiarized Research</div>
                                        <?php
                                        $count = $db->SELECT_COUNT_ALL_PLAGIARIZED_RESEARCH();

                                        ?>
                                        <div class="stat-digit"><?php echo $count ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-----------CHART-------------->
                    

                    <!-- <div class="row">
                    <?php
                        $rows = $db->Archive_Research_BasedOn_Department();

                        $department_name = array_column($rows, 'name');
                        $department_counts = array_column($rows, 'count');

                        // Display course IDs and counts side by side
                        for ($i = 0; $i < count($department_name); $i++) {
                            // echo "Total Research of " . $department_name[$i] . ": " . $department_counts[$i] . "<br>";
                    ?>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="stat-widget-one">
                                    <div class="stat-icon dib"><i class="ti-files color-primary border-primary"></i>
                                    </div>
                                    <div class="stat-content dib">
                                        <div class="stat-text"><?php echo "" . $department_name[$i] ?></div>
                                        
                                        <div class="stat-digit"><?php echo $department_counts[$i] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    </div> -->
                    <!-----------CHART-------------->
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="card">
                            <div class="card-title">
                                <h4>Research Per College </h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="barChart2"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>

                        <div class="col-lg-12">
                            <div class="card">
                            <div class="card-title">
                                <h4>Research Per Course</h4>
            
                            </div>
                            <div class="sales-chart">
                                <canvas id="barChart"></canvas>
                            </div>
                            </div>
                            <!-- /# card -->
                        </div>

                        <div class="col-lg-12">
                            <div class="card">
                            <div class="card-title">
                                <h4>Research Views Per College </h4>

                            </div>
                            <div class="sales-chart">
                                <canvas id="sales-chart2"></canvas>
                            </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card">
                            <div class="card-title">
                                <h4>Research Views Per Course </h4>

                            </div>
                            <div class="sales-chart">
                                <canvas id="sales-chart"></canvas>
                            </div>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- jquery vendor -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <!-- nano scroller -->
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <!-- sidebar -->

    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>
    <!-- bootstrap -->

    <script src="js/lib/calendar-2/moment.latest.min.js"></script>
    <script src="js/lib/calendar-2/pignose.calendar.min.js"></script>
    <script src="js/lib/calendar-2/pignose.init.js"></script>

    <script src="js/lib/chart-js/Chart.bundle.js"></script>
    <!-- <script src="js/lib/chart-js/chartjs-init.js"></script> -->

    <!-- scripit init-->
    <script src="js/dashboard2.js"></script>

    
    <script src="js/lib/sweetalert/sweetalert.min.js"></script>
    <script src="js/lib/sweetalert/sweetalert.init.js"></script>



    
    <?php


$rows = $db->Archive_Research_BasedOn_Course();

$course_id = array_column($rows, 'course_name');
$course_counts = array_column($rows, 'count');
?>

<script>
    (function($) {
        "use strict";

        
        var course_id = <?= json_encode($course_id) ?>;
        var course_counts = <?= json_encode($course_counts) ?>;


        var ctx = document.getElementById("barChart");
        ctx.height = 180;

        // Assuming department_name and department_counts are arrays containing department names and counts
        // Define an array of 20 colors (one for each department)
        var colors = [
            "rgba(156, 39, 176, 0.8)",
            "rgba(0, 150, 136, 0.8)",
            "rgba(76, 175, 80, 0.8)",
            "rgba(121, 85, 72, 0.8)",
            "rgba(244, 67, 54, 0.8)",
            "rgba(255, 152, 0, 0.8)",
            "rgba(63, 81, 181, 0.8)",
            "rgba(0, 123, 255, 0.8)",
            "rgba(255, 99, 132, 0.8)",
            "rgba(54, 162, 235, 0.8)",
            "rgba(255, 206, 86, 0.8)",
            "rgba(75, 192, 192, 0.8)",
            "rgba(153, 102, 255, 0.8)",
            "rgba(255, 159, 64, 0.8)",
            "rgba(255, 87, 34, 0.8)",
            "rgba(3, 169, 244, 0.8)",
            "rgba(233, 30, 99, 0.8)",
            "rgba(33, 150, 243, 0.8)",
            "rgba(255, 193, 7, 0.8)",
            "rgba(255, 235, 59, 0.8)"
        ];

        var datasets = [];

        for (var i = 0; i < course_id.length; i++) {
            datasets.push({
                label: course_id[i],
                data: [course_counts[i]],
                backgroundColor: colors[i % colors.length], // Set color based on index, repeating colors if needed
                borderColor: colors[i % colors.length],
                borderWidth: 1
            });
        }

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: datasets
            },
            options: {
                responsive: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });




<?php
$rows = $db->Archive_Research_BasedOn_Department();

$department_name = array_column($rows, 'name');
$department_counts = array_column($rows, 'count');
?>

        var department_name = <?= json_encode($department_name) ?>;
        var department_counts = <?= json_encode($department_counts) ?>;

    //bar chart
    var ctx = document.getElementById("barChart2");
    ctx.height = 120;

    // Assuming department_name and department_counts are arrays containing department names and counts
    // Define an array of 20 colors (one for each department)
    var colors = [
        "rgba(0, 123, 255, 0.8)",
        "rgba(255, 99, 132, 0.8)",
        "rgba(54, 162, 235, 0.8)",
        "rgba(255, 206, 86, 0.8)",
        "rgba(75, 192, 192, 0.8)",
        "rgba(153, 102, 255, 0.8)",
        "rgba(255, 159, 64, 0.8)",
        "rgba(255, 87, 34, 0.8)",
        "rgba(3, 169, 244, 0.8)",
        "rgba(233, 30, 99, 0.8)",
        "rgba(33, 150, 243, 0.8)",
        "rgba(255, 193, 7, 0.8)",
        "rgba(156, 39, 176, 0.8)",
        "rgba(0, 150, 136, 0.8)",
        "rgba(76, 175, 80, 0.8)",
        "rgba(121, 85, 72, 0.8)",
        "rgba(244, 67, 54, 0.8)",
        "rgba(255, 152, 0, 0.8)",
        "rgba(63, 81, 181, 0.8)",
        "rgba(255, 235, 59, 0.8)"
    ];

    var datasets = [];

    for (var i = 0; i < department_name.length; i++) {
        datasets.push({
            label: department_name[i],
            data: [department_counts[i]],
            backgroundColor: colors[i % colors.length], // Set color based on index, repeating colors if needed
            borderColor: colors[i % colors.length],
            borderWidth: 1
        });
    }

    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });




    <?php

$result = $db->Archive_Research_Views_BasedOn_Course();

$dates = [];
$courseCounts = [];

foreach ($result as $row) {
$date = $row['date_of_views'];
$course = $row['course_name'];
$count = $row['count'];

if (!array_key_exists($date, $dates)) {
    $dates[$date] = [];
}

$dates[$date][$course] = $count;
if (!in_array($course, $courseCounts)) {
    $courseCounts[] = $course;
}
}

?>

var dates = <?= json_encode($dates) ?>;
var courseCounts = <?= json_encode($courseCounts) ?>;

// Extract labels and datasets dynamically from PHP data
var labels = Object.keys(dates);
var datasets = [];

courseCounts.forEach(function(course) {
    var dataPoints = [];
    labels.forEach(function(date) {
        if (dates[date][course] !== null) {  // Check for null values
            dataPoints.push(dates[date][course]);
        } else {
            dataPoints.push(0);  // Set a default value for null entries
        }
    });

    
    datasets.push({
        label: course,
        data: dataPoints,
        backgroundColor: 'transparent',
        borderColor: getRandomColor(),
        borderWidth: 4,
        pointStyle: 'circle',
        pointRadius: 6,
        pointBorderColor: 'transparent',
        pointBackgroundColor: getRandomColor(),
    });
});



function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

// Sales chart
var ctx = document.getElementById("sales-chart");
ctx.height = 150;
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        type: 'line',
        defaultFontFamily: 'Montserrat',
        datasets: datasets,
    },
    options: {
        responsive: true,
        tooltips: {
            mode: 'index',
            titleFontSize: 12,
            titleFontColor: '#000',
            bodyFontColor: '#000',
            backgroundColor: '#fff',
            titleFontFamily: 'Montserrat',
            bodyFontFamily: 'Montserrat',
            cornerRadius: 3,
            intersect: false,
        },
        legend: {
            display: true,
            labels: {
                usePointStyle: true,
                fontFamily: 'Montserrat',
            },
        },
        scales: {
            xAxes: [{
                display: true,
                gridLines: {
                    display: true,
                    drawBorder: false
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Dates'
                }
            }],
            yAxes: [{
                display: true,
                gridLines: {
                    display: true,
                    drawBorder: false
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Views'
                }
            }]
        },
        elements: {
            line: {
                tension: 0,  // Set tension to 0 to create a straight line
            }
        },
        title: {
            display: false,
            text: 'Normal Legend'
        }
    }
});



<?php
    
    $result = $db->Archive_Research_Views_BasedOn_Departments();
    
    $dates = [];
    $departmentCounts = [];
    
    foreach ($result as $row) {
    $date = $row['date_of_views'];
    $department = $row['name'];
    $count = $row['count'];
    
    if (!array_key_exists($date, $dates)) {
        $dates[$date] = [];
    }
    
    $dates[$date][$department] = $count;
    if (!in_array($department, $departmentCounts)) {
        $departmentCounts[] = $department;
    }
    }
    
    ?>
    
    var dates = <?= json_encode($dates) ?>;
    var departmentCounts = <?= json_encode($departmentCounts) ?>;
    
    // Extract labels and datasets dynamically from PHP data
    var labels = Object.keys(dates);
    var datasets = [];
    
    departmentCounts.forEach(function(department) {
    var dataPoints = [];
    labels.forEach(function(date) {
        if (dates[date][department] !== null) {  // Check for null values
            dataPoints.push(dates[date][department]);
        } else {
            dataPoints.push(0);  // Set a default value for null entries
        }
    });

    datasets.push({
        label: department,
        data: dataPoints,
        backgroundColor: 'transparent',
        borderColor: getRandomColor(),
        borderWidth: 3,
        pointStyle: 'circle',
        pointRadius: 5,
        pointBorderColor: 'transparent',
        pointBackgroundColor: getRandomColor(),
    });
});
    
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
    
    var ctx = document.getElementById("sales-chart2");
    ctx.height = 150;
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            type: 'line',
            defaultFontFamily: 'Montserrat',
            datasets: datasets,
        },
        options: {
            responsive: true,
            tooltips: {
                mode: 'index',
                titleFontSize: 12,
                titleFontColor: '#000',
                bodyFontColor: '#000',
                backgroundColor: '#fff',
                titleFontFamily: 'Montserrat',
                bodyFontFamily: 'Montserrat',
                cornerRadius: 3,
                intersect: false,
            },
            legend: {
                display: true,
                labels: {
                    usePointStyle: true,
                    fontFamily: 'Montserrat',
                },
            },
            scales: {
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: true,
                        drawBorder: false
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Dates'
                    }
                }],
                yAxes: [{
                    display: true,
                    gridLines: {
                        display: true,
                        drawBorder: false
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Views'
                    }
                }]
            },
            elements: {
            line: {
                tension: 0,  // Set tension to 0 to create a straight line
            }
        },
            title: {
                display: false,
                text: 'Normal Legend'
            }
        }
    });

    

    })(jQuery);



    var ctx = document.getElementById('departmentChart').getContext('2d');
    var departmentChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Active Departments', 'Inactive Departments'],
            datasets: [{
                label: 'Department Status',
                data: [<?php echo $activePercentage; ?>, <?php echo $inactivePercentage; ?>],
                backgroundColor: ['#4CAF50', '#FF6384'],
                borderColor: ['#4CAF50', '#FF6384'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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

</body>

</html>
