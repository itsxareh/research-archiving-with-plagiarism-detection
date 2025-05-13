<?php 
include '../connection/config.php';
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = $_POST["email"];

    $stmt = $db->student_register_select_email($email);
    $exists = boolval($stmt);
  
    echo json_encode(["exists" => $exists]);
  }
?>