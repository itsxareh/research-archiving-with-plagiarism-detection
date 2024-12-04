<?php 
include '../../connection/config.php';
include 'helper.php';

if ($departmentId != 0){
    $student_list = $db->SELECT_TOP_RESEARCH_CONTRIBUTOR_WHERE_DEPARTMENT($departmentId);
} else {
    $student_list = $db->SELECT_TOP_RESEARCH_CONTRIBUTOR();
}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($departmentId != 0 ? $db->getDepartmentById($departmentId)['name'] : 'All Departments'); ?> - Top Contributor</title>
    <link rel="stylesheet" href="../../css/styles.css"/>
    <link rel="shortcut icon" href="../images/logo2.webp">
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
        <h1><?php echo htmlspecialchars($departmentId != 0 ? $db->getDepartmentById($departmentId)['name'] : 'All Departments'); ?></h1>
        <h3>Research Paper Top Contributor</h3>
        <p>Generated on: <?php echo $current_date_time; ?></p>
    </div>
    
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 35%">Name</th>
                    <th style="width: 45%">Email</th>
                    <th style="width: 20%" class="text-center">No. of Published Paper</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if (!empty($student_list)){
                foreach ($student_list as $student){
                    if ($student['first_name'] == '' && $student['last_name'] == ''){
                        $student['first_name'] = '-';
                    }
            ?>  
                <tr>
                    <td class="text-center" ><?php echo htmlspecialchars($student['first_name'].' '.$student['middle_name'].' '.$student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['research_owner_email']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars($student['count']); ?></td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='3' class='text-center'>No available data found</td></tr>";
            }
            ?>
            </tbody>
        </table>
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