<?php 
include '../connection/config.php';
$db = new Database();

if (isset($_POST['department_code'])){
    $department_name = trim($_POST['department_code']);
    $exclude_id = isset($_POST['exclude_id']) ? trim($_POST['exclude_id']) : '';
    $select_department = $db->SELECT_DEPARTMENT_BY_NAME($department_name, $exclude_id);

    if($select_department){
        echo json_encode(['exists' => true]);
    }else{
        echo json_encode(['exists' => false]);
    }
}
?>