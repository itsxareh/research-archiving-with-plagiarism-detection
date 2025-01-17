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
    <link rel="shortcut icon" href="../images/logo2.webp">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #2d3748;
        }

        .receipt-container {
            max-width: 900px;
            margin: 10px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .receipt-header {
            background-color: #f8fafc;
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .receipt-logo {
            max-width: 100px;
            height: auto;
        }

        .receipt-title {
            text-align: center;
            margin: 16px 0;
        }

        .receipt-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 10px;
        }

        .receipt-title p {
            color: #64748b;
            font-size: 1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .receipt-body {
            padding: 20px;
        }

        .detail-section {
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        .detail-value {
            font-size: 1rem;
            color: #1a365d;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 4px 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .btn i {
            font-size: 1rem;
        }

        .btn-outline-primary {
            border-color: #3182ce;
            color: #3182ce;
        }

        .btn-outline-primary:hover {
            background-color: #3182ce;
            color: white;
            transform: translateY(-1px);
        }

        .btn-outline-success {
            border-color: #38a169;
            color: #38a169;
        }

        .btn-outline-success:hover {
            background-color: #38a169;
            color: white;
            transform: translateY(-1px);
        }
        .footer {
            padding: 20px;
            text-align: right;
        }
        .footer p {
            color: #888;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .receipt-container {
                margin: 20px;
            }

            .header-content {
                flex-direction: column;
                gap: 20px;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                width: 100%;
                flex-direction: column;
            }
        }

        @media print {
            body {
                background-color: white;
            }
            
            .receipt-container {
                margin: 0;
                box-shadow: none;
            }

            .receipt-header {
                background-color: white;
            }

            .detail-section {
                background-color: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-title">
                <h1><span class="detail-value"><?php echo htmlspecialchars($row['project_title']); ?></span></h1>
                <p>Research Submission Receipt</p>
            </div>
        </div>

        <div class="receipt-body">
            <div class="detail-section">
                <h2 class="section-title">Submission Details</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Archive ID</span>
                        <span class="detail-value"><?php echo htmlspecialchars($row['archiveID']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date & Time of Submission</span>
                        <span class="detail-value"><?php echo htmlspecialchars((new DateTime($row['dateOFSubmit']))->format('d M Y H:i:s A')); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Author</span>
                        <span class="detail-value"><?php echo htmlspecialchars($row['fname']. ' ' .$row['mname']. ' ' .$row['lname']); ?></span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h2 class="section-title">Document Information</h2>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">File Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($document_name); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">File Size</span>
                        <span class="detail-value"><?php echo htmlspecialchars(formatSize($row['file_size'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Page Count</span>
                        <span class="detail-value"><?php echo htmlspecialchars($row['page_count']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Word Count</span>
                        <span class="detail-value"><?php echo htmlspecialchars($row['word_count']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Character Count</span>
                        <span class="detail-value"><?php echo htmlspecialchars($row['character_count']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Similarity Index</span>
                        <span class="detail-value"><?php echo htmlspecialchars($row['total_percentage'] > 100 ? 100 : ($row['total_percentage'] == 0 ? 0 : round($row['total_percentage'], 2))); ?>%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <p>EARIST Repository - Generated on: <?php echo $current_date_time; ?></p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();
echo $html;
}