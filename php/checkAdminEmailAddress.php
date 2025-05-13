<?php 
include '../connection/config.php';
$db = new Database();
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $email = $_POST["email"];
    

    $stmt = $db->admin_register_select_email($email, $admin_id);
    $exists = boolval($stmt);
  
    echo json_encode(["exists" => $exists]);
  }
?>