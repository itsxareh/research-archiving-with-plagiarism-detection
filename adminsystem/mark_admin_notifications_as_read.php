<?php
include '../connection/config.php';

$db = new Database();

session_start();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_SESSION['auth_user']['admin_id'])) {
    $admin_id = $_SESSION['auth_user']['admin_id'];

    $read = 'Read';
    // Adjust your SQL query to update notifications as read based on your database schema

    $readALL = $db->adminsystemNOTIFICATION_MarkASRead($read, $admin_id);

    // Respond to the AJAX request with a JSON response
    $response = array("success" => true);
    echo json_encode($response);
    exit;
}
?>
