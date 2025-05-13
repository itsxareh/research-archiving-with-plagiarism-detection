<?php 
include '../connection/config.php';
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["snumber"])) {
    $snumber = $_POST["snumber"];
    $snumberPattern = '/^\d{3}-\d{5}[A-Za-z]$/';

    if (!preg_match($snumberPattern, $_POST['snumber'])) {
        echo json_encode(["exists" => true, "message" => "Invalid student number format."]);
        exit;
    }
    $stmt = $db->student_register_select_StudentNumber($snumber);
    $exists = boolval($stmt);
  
    echo json_encode(["exists" => $exists, "message" => "Student number is already registered."]);
  }
?>