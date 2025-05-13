<?php 
include '../connection/config.php';
$db = new Database();

header("Content-Type: application/json"); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["password"])) {
    $password = trim($_POST["password"]);
    $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&.-=+_])[A-Za-z\d@$!%*?&.-=+_]{8,}$/';  

    if (!preg_match($passwordPattern, $password)) {
      echo json_encode(["valid" => false, "message" => "Password must be at least 8 characters long and include a mix of letters, numbers, & symbols."]);
    } else {
        echo json_encode(["valid" => true, "message" => ""]);
    }
  exit;
  }
?>