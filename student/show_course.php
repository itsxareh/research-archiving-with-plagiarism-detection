<?php
include '../connection/config.php';
$db = new Database();

if(ISSET($_POST['send_department_set'])){

    $department = $_POST['send_department'];

    if ($department){
        $stmt = $db->showCourse_WHERE_ACTIVE($department);
        echo '<option value=""></option>';
        foreach ($stmt as $item) {
            echo '<option value="'.$item['id'].'">'.$item['course_name'].'</option>';
        }
    }
}
?>