<?php 
include '../connection/config.php';
$db = new Database();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
  $email = trim($_POST["email"]);
  $current_email = trim($_POST["current_email"]);
  $studID = $_POST["studID"];

  error_log("Checking email - New: $email, Current: $current_email, StudID: $studID");

  $stmt = $db->check_student_email($email, $studID);
  $exists = boolval($stmt);

  echo json_encode(array(
      "message" => $stmt, 
      "exists" => $exists,
      "debug" => array(
          "new_email" => $email,
          "current_email" => $current_email,
          "student_id" => $studID
      )
  ));
  }
?>