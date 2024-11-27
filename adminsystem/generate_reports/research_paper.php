<?php
include '../../connection/config.php';
$db = new Database();
session_start();
if($_SESSION['auth_user']['admin_id']==0){
    header('Location:../../bad-request.php');
    exit();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_GET['archiveID'])) {
    $archive_id = $_GET['archiveID'];
    $row = $db->SELECT_ARCHIVE_RESEARCH($archive_id);
    if ($row['fname'] == '') {
        $row = $db->SELECT_UPLOADED_ADMIN_ARCHIVE_RESEARCH($archive_id);
    }
    $path = $row['documents'];
    preg_match('/[a-zA-Z0-9]+-([\s\S]+)$/', $path, $matches);
    $document_name = $matches[1];

    function formatSize($bytes) {
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf('%.2f', $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }
    
    date_default_timezone_set('Asia/Manila');
    $current_date_time = date('Y-m-d H:i:s A');

ob_start()
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Receipt</title>
    <link rel="shortcut icon" href="../images/logo2.png">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            padding: 10px 0;
            font-size: 18px;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            margin-top: 100px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .receipt-logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .receipt-details {
            text-align: center;
            border-top: 1px solid #dee2e6;
            margin-top: 20px;
            padding-top: 20px;

        }
        .detail-row {
            display: flex;
            gap: 4px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .detail-label {
            color: #6c757d;
        }
        .detail-value {
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1 class="h3 mb-4"><?php echo ucwords(htmlspecialchars($row['project_title'])) ?></h1>
        </div>
        <div class="receipt-details">
            <div class="detail-row">
                <div class="detail-label">Archive ID:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['archiveID']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Uploader name:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['fname']. ' ' .$row['mname']. ' ' .$row['lname']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">File name:</div>
                <div class="detail-value"><?php echo htmlspecialchars($document_name); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">File size:</div>
                <div class="detail-value"><?php echo htmlspecialchars(formatSize($row['file_size'])); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Page count:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['page_count']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Word count:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['word_count']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Character count:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['character_count']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date uploaded:</div>
                <div class="detail-value"><?php echo htmlspecialchars((new DateTime($row['dateOFSubmit']))->format('d M Y H:i:s A')); ?></div>
            </div>
            
        </div>
        <!-- QR Code for verification 
        <div class="text-center mt-8">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode('submission_verification.php?archiveID=' . $row['archiveID']); ?>" 
                 alt="Verification QR Code" 
                 class="qr-code">
            <p class="small text-muted mt-2">Scan to verify submission</p>
        </div> 
        -->
        <div class="footer">
            <p>EARIST Research Archive - Generated on: <?php echo $current_date_time; ?></p>
        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();
echo $html;
}