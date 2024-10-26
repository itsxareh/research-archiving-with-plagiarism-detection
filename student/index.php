<?php

include '../connection/config.php';
error_reporting(0);

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EARIST Research Archiving System</title>
</head>
<body>
    <?php 
    include 'templates/student_navbar.php'
    ?>

    <div id="main">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 'all_project_list';
        }
        ?>
    </div>
</body>
</html>