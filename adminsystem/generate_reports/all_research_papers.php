<?php 
include '../../connection/config.php';
$db = new Database();
session_start();
date_default_timezone_set('Asia/Manila');
$current_date_time = date('Y-m-d H:i:s A');

function hasPermit($permissions, $permissionToCheck) {
    foreach ($permissions as $permission) {
        if (strpos($permission, $permissionToCheck) === 0) {
            return true;
        }
    }
    return false;
}
$userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
$departmentId = $userRole['department_id'];
$permissions = explode(',', $userRole['permissions']);
if (!hasPermit($permissions, 'research_view')) {
    header('Location:../../bad-request.php');
    exit();
} elseif ($_SESSION['auth_user']['admin_id']==0){
    header('Location:../../bad-request.php');
    exit();
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $filterByDepartment = ($departmentId != 0);
}


if($departmentId != 0){
    $student_list = $db->SELECT_ALL_ARCHIVE_RESEARCH_WHERE_DEPARTMENT_IS($departmentId);
} else {
    $student_list = $db->SELECT_ALL_ARCHIVE_RESEARCH();
}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($departmentId != 0 ? $db->getDepartmentById($departmentId)['name'] : 'All Departments'); ?> - Research Paper's List</title>
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
        <h3>Research Paper's List</h3>
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
                    <th style="width: 10%;">Plagiarism</th>
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
                    <td class="text-center"><?php echo htmlspecialchars(isset($student['aid']) ? $student['aid'] : 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(isset($student['project_title']) ? $student['project_title'] : 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(isset($student['dept_code']) ? $student['dept_code'] : 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(isset($student['course_name']) ? $student['course_name'] : 'N/A'); ?></td>
                    <td class="text-center"><?php echo htmlspecialchars(isset($student['plagiarism_percentage']) ? ($student['plagiarism_percentage'] === NULL ? 'No' : ($student['plagiarism_percentage'] > 100 ? '100%' : round($student['plagiarism_percentage'], 2).'%')) : 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars(isset($student['dateOFSubmit']) ? (new DateTime($student['dateOFSubmit']))->format("d M Y") : 'N/A'); ?></td>
                    <td class="text-center <?php echo $status_class; ?>"><?php echo htmlspecialchars(isset($student['document_status']) ? $student['document_status'] : 'N/A'); ?></td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No available data found</td></tr>";
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