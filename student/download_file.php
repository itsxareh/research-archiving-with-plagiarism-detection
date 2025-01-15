<?php
include '../connection/config.php';
$db = new Database();
session_start();

$archiveID = isset($_GET['archiveID']) ? $_GET['archiveID'] : (isset($_POST['archiveID']) ? $_POST['archiveID'] : null);
$user_id = isset($_SESSION['auth_user']['student_id']) ? $_SESSION['auth_user']['student_id'] : '';

if (!$archiveID) {
    die('No file specified');
}

$file = $db->SELECT_ARCHIVE_RESEARCH($archiveID);

if (!$file) {
    die('File not found');
}
$path = $file['documents'];
preg_match('/[a-zA-Z0-9]+-([\s\S]+)$/', $path, $matches);
$document_name = $matches[1];

$filepath = $file['documents']; 
$filename = $document_name;  

if (!file_exists($filepath)) {
    die('File does not exist');
}

date_default_timezone_set('Asia/Manila');
$current_date_time = date('Y-m-d H:i:s');
$stmt = $db->INSERT_STUDENT_DOWNLOAD_LOGS($user_id, $archiveID, $current_date_time);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');

ob_clean();
flush();

if ($fp = fopen($filepath, 'rb')) {
    while (!feof($fp) && connection_status() == 0) {
        print(fread($fp, 8192));
        flush();
    }
    fclose($fp);
}
exit;