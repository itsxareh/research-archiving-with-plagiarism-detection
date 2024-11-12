<?php
include '../connection/config.php';
$db = new Database();

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if(isset($_GET['archiveID'])) {
    $archive_id = $_GET['archiveID'];
    $row = $db->SELECT_ARCHIVE_RESEARCH($archive_id);

    $path = $row['documents'];
    preg_match('/[a-zA-Z0-9]+-([\s\S]+)$/', $path, $matches);
    $document_name = $matches[1];

    function formatSize($bytes) {
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf('%.2f', $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Receipt</title>
    <link rel="shortcut icon" href="images/logo2.png">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
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
            border-top: 1px solid #dee2e6;
            margin-top: 20px;
            padding-top: 20px;
        }
        .detail-row {
            display: grid;
            gap: 4px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 8px;
        }
        .detail-label {
            color: #6c757d;
        }
        .detail-value {
        }
        @media print {
            body {
                background-color: white;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none !important;
            }
            .receipt-details {
                break-inside: avoid;
            }
        }
        .download-button {
            color: #28a745;
            text-decoration: none;
            margin-left: 10px;
        }

        .download-button:hover {
            color: #218838;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <img src="images/logo2.png" alt="Logo" class="receipt-logo">
            <div class="action-buttons no-print">
                <button onclick="printReceipt()" class="print-btn btn btn-outline-primary">
                    <i class="fas fa-print me-2"></i>Print
                </button>
                <button onclick="downloadPDF()" class="download-btn btn btn-outline-success">
                    <i class="fas fa-download me-2"></i>Download PDF
                </button>   
            </div>
        </div>

        <h1 class="h3 mb-4">Digital Receipt</h1>
        
        <p class="text-muted">
            This receipt acknowledges that we received your paper. Below you will find the receipt
            information regarding your submission.
        </p>

        <div class="receipt-details">
            <div class="detail-row">
                <div class="detail-label">Uploader name:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['fname']. ' ' .$row['mname']. ' ' .$row['lname']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Project title:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['project_title']); ?></div>
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
            <div class="detail-row">
                <div class="detail-label">Archive ID:</div>
                <div class="detail-value"><?php echo htmlspecialchars($row['archiveID']); ?></div>
            </div>
        </div>

        <!-- QR Code for verification -->
        <div class="text-center mt-8">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode('submission_verification.php?archiveID=' . $row['archiveID']); ?>" 
                 alt="Verification QR Code" 
                 class="qr-code">
            <p class="small text-muted mt-2">Scan to verify submission</p>
        </div>
    </div>

    <!-- Include html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        // Print functionality
        function printReceipt() {
            window.print();
        }

        function downloadPDF() {
            const noPrint = document.querySelector('.print-btn');
            const noDownload = document.querySelector('.download-btn');
            const element = document.querySelector('.receipt-container');


            noPrint.style.display = 'none';
            noDownload.style.display = 'none';

            const opt = {
                margin: 1,
                filename: '<?php echo $row['archiveID']; ?>-digital-receipt.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            // Generate PDF
            html2pdf().set(opt).from(element).save().then(() => {
                noPrint.style.display = 'inline-block';
                noDownload.style.display = 'inline-block';
            });
        }

        // window.onbeforeunload = function() {
        //     return "Make sure you have downloaded or printed your receipt if needed.";
        // };
    </script>
</body>
</html>
<?php
}