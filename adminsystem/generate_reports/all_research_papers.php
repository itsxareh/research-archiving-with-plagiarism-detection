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

$student_list = $db->SELECT_ALL_ARCHIVE_RESEARCH();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EARIST Research Archive - Research Paper's List</title>
    <link rel="stylesheet" href="../../css/styles.css"/>
    <link rel="shortcut icon" href="../images/logo2.png">
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
        <h1>EARIST Research Archive - Research Paper's List</h1>
        <p>Generated on: <?php echo $current_date_time; ?></p>
    </div>
    
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th style="width: 12%;">Archive ID</th>
                    <th style="width: 30%;">Research title</th>
                    <th style="width: 10%;">Department</th>
                    <th style="width: 12%;">Course</th>
                    <th style="width: 10%;">Plagiarized</th>
                    <th style="width: 12%;">Date uploaded</th>
                    <th style="width: 10%;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if (!empty($student_list)){
                foreach ($student_list as $student){
                    $status_class = $student['document_status'] === 'Accepted' ? 'status-approved' : 'status-not-approved';
            ?>
                <tr>
                    <td class="text-center"><?php echo htmlspecialchars($student['aid']); ?></td>
                    <td><?php echo htmlspecialchars($student['project_title']); ?></td>
                    <td><?php echo htmlspecialchars($student['dept_code']); ?></td>
                    <td><?php echo htmlspecialchars($student['course_name']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($student['plagiarism_percentage'] === NULL ? 'No' : 'Yes'); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($student['dateOFSubmit']))->format("d M Y")); ?></td>
                    <td class="text-center <?php echo $status_class; ?>"><?php echo htmlspecialchars($student['document_status']); ?></td>
                </tr>
            <?php 
                }
            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>EARIST Research Archive - Generated Report</p>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();
echo $html;
?>