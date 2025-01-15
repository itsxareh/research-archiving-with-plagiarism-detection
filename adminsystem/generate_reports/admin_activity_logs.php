<?php 
include '../../connection/config.php';
$db = new Database();
session_start();
if($_SESSION['auth_user']['admin_id']==0){
    header('Location:../../bad-request.php');
    exit(); 
}
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Manila');
$current_date_time = date('Y-m-d H:i:s A');

if (isset($_GET['adminID'])){
    $adminID = $_GET['adminID'];
} else {
    $adminID = $_SESSION['auth_user']['admin_id'];
}
$admin_log = $db->admin_activity_log($adminID);

function isValidDateFormat($date) {
    // Regular expression to match 'Month / Day Weekday / Year'
    return preg_match('/^[A-Za-z]+ \/ \d{2} [A-Za-z]+ \/ \d{4}$/', $date);
}

// Organize logs by date
if ($admin_log[0]['logs_date'] != null){
    $organized_logs = [];
    foreach ($admin_log as $log) {
        if (!isValidDateFormat($log['logs_date'])) {
            continue;
        }

        $date_parts = explode(' / ', $log['logs_date']);
        $month = trim($date_parts[0]);
        $day = explode(' ', trim($date_parts[1]))[0];
        $year = trim($date_parts[2]);
        
        $date_key = sprintf('%s-%02d-%s', $year, date('m', strtotime($month)), $day);
    
        $modified_log = $log;
        $modified_log['logs'] = str_replace('You', '', $log['logs']);
        
        if (!isset($organized_logs[$date_key])) {
            $organized_logs[$date_key] = [
                'display_date' => $log['logs_date'],
            'logs' => []
            ];
        }
        $organized_logs[$date_key]['logs'][] = $modified_log;
    }
    
    krsort($organized_logs);
}

// Sort by date (newest first)
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EARIST Repository - Admin's Activity Logs</title>
    <link rel="shortcut icon" href="../images/logo2.webp">
    <link rel="stylesheet" href="../../css/styles.css"/>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* This ensures the column widths are respected */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 4px;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            vertical-align: middle;
        }
        thead tr:first-child th {
            border-bottom: none;
        }

        thead tr:last-child th {
            border-top: none;
        }
                .footer {
            text-align: right;
            font-size: 10px;
            margin-top: 20px;
        }
        .status-approved {
            color: green;
            font-weight: bold;
        }
        .status-not-approved {
            color: red;
            font-weight: bold;
        }
        /* Column specific styles */
        .student-number {
            width: 15%;
        }
        .full-name {
            width: 25%;
        }
        .email {
            width: 30%;
        }
        .research-count {
            width: 10%;
            text-align: center;
        }
        .status {
            width: 10%;
            text-align: center;
        }
        /* Ensure text doesn't overflow */
        td {
            word-break: break-word;
            vertical-align: middle;
        }
        /* Center align numeric columns */
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo $admin_log[0]['first_name'].' '.$admin_log[0]['last_name']; ?></h1>
        <h3>Activity Logs</h3>
        <p>Generated on: <?php echo $current_date_time; ?></p>
    </div>
    
    <div class="table">
        <?php 
        if (!empty($organized_logs)){
            foreach($organized_logs as $date_key => $date_group): ?>
                <!-- Date Header -->
                <p style="font-size: 12px; margin: 10px 0 0 0; font-weight: bold;"><?php echo htmlspecialchars($date_group['display_date']); ?></p>
                <?php foreach($date_group['logs'] as $log): ?>
                <div class="">
                    <p style="font-size: 11px;"><?php echo htmlspecialchars($log['logs_time']); ?> - <?php echo htmlspecialchars(ucwords($log['logs'])); ?></p>
                </div>
            
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php 
            
        } else {
            echo "<p class='text-center'>No available log found</p>";
        }
        ?>
    </div>

    <div class="footer">
        <p>EARIST Repository - Generated Report</p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();
echo $html;
?>