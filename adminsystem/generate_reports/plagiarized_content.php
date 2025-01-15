<?php 
include '../../connection/config.php';
include 'helper.php';

if ($departmentId != 0){
    $student_list = $db->SELECT_PLAGIARIZED_RESEARCH_CONTENT_WHERE_DEPARTMENT($departmentId);
} else {
    $student_list = $db->SELECT_PLAGIARIZED_RESEARCH_CONTENT();
}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($departmentId != 0 ? $db->getDepartmentById($departmentId)['name'] : 'All Departments'); ?> - Plagiarized Content</title>
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
        <h3>Plagiarized Content</h3>
        <p>Generated on: <?php echo $current_date_time; ?></p>
    </div>
    
    <div class="table">
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 12%">Archive ID</th>
                    <th class="text-center" style="width: 10%">Date Uploaded</th>
                    <th style="width: 50%">Research Title</th>
                    <th style="width: 15%" class="text-center">Plagiarized Content Percentage </th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if (!empty($student_list)){
                foreach ($student_list as $student){
                    $plagiarism_percentage = $student['total_percentage'];
                    if ($plagiarism_percentage > 100){
                        $plagiarism_percentage = 100;
                    }
            ?>  
                <tr>
                    <td class="text-center" ><?php echo htmlspecialchars($student['aid']); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($student['dateOFSubmit']))->format("d M Y")); ?></td>
                    <td><?php echo htmlspecialchars($student['project_title']); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars(round($plagiarism_percentage, 2)); ?>%</td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No available data found</td></tr>";
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