<?php 
include '../connection/config.php';
$db = new Database();

if (isset($_POST['course_name'])){
    $course_name = trim($_POST['course_name']);
    $select_course = $db->SELECT_COURSE_BY_NAME($course_name);

    if($select_course){
        echo json_encode(['exists' => true]);
    }else{
        echo json_encode(['exists' => false]);
    }
}
?>