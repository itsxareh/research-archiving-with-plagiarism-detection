<?php

include '../connection/config.php';

$db = new Database();
if(ISSET($_POST['send_department_set'])){
    $departmentId = $_POST['send_department'];
    $stmt = $db->showCourse_WHERE_ACTIVE($departmentId);

    foreach ($stmt as $item) {
    echo '<option value="'.$item['id'].'">'.$item['course_name'].'</option>';
    }
}
?>
