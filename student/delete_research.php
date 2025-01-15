<?php

include '../connection/config.php';
session_start();

$db = new Database();

if(isset($_GET['archiveID'])){

    $archive_id = $_GET['archiveID'];

    $sql1 = $db->delete_research($archive_id);

    if ($sql1) {
        echo json_encode(["status" => "success", "message" => "Research deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error deleting research"]);
    }

}