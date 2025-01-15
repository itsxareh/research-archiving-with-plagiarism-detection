<?php
include '../connection/config.php';

$db = new Database();

session_start();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (isset($_SESSION['auth_user']['student_id'])) {
    $archive_id = $_POST['archiveID'];

    $read = 1;

    $readALL = $db->studentINBOX_MarkASRead($read, $archive_id);

    if ($readALL){
        $response = array("success" => true);
        echo json_encode($response);
        exit;
    }
}
?>
