<?php
if($_SESSION['auth_user']['student_id']==0){
  echo"<script>window.location.href='login.php'</script>";
  
}


if(isset($_SESSION['auth_user']['student_id'])) {
    $student_id = $_SESSION['auth_user']['student_id'];
    $activity = $db->student_activity_log($student_id);
    
    // Organize logs by date
    function isValidDateFormat($date) {
        // Regular expression to match 'Month / Day Weekday / Year'
        return preg_match('/^[A-Za-z]+ \/ \d{2} [A-Za-z]+ \/ \d{4}$/', $date);
    }
    
    // Organize logs by date
    $organized_logs = [];
    foreach ($activity as $log) {
        if (!isValidDateFormat($log['logs_date'])) {
            continue;
        }
        
        $date_parts = explode(' / ', $log['logs_date']);
        $month = trim($date_parts[0]);
        $day = explode(' ', trim($date_parts[1]))[0];
        $year = trim($date_parts[2]);
        
        $date_key = sprintf('%s-%02d-%s', $year, date('m', strtotime($month)), $day);
        
        $modified_log = $log;
        if ($log['student_id'] != $student_id) {
            $student_name = $db->get_student_name_by_id($log['student_id']);
            $modified_log['logs'] = str_replace('You', $student_name, $log['logs']);
        }
        
        if (!isset($organized_logs[$date_key])) {
            $organized_logs[$date_key] = [
                'display_date' => $log['logs_date'],
                'logs' => []
            ];
        }
        $organized_logs[$date_key]['logs'][] = $modified_log;
    }
    // Sort by date (newest first)
    krsort($organized_logs);
}
?>

<strong>Activity Logs</strong><br>
<div class="card">
    <div class="list-group list-group-flush">
        <?php if(!empty($organized_logs)): ?>
            <?php foreach($organized_logs as $date_key => $date_group): ?>
                <!-- Date Header -->
                <div class="list-group-item bg-light">
                    <div class="d-flex align-items-center">
                        <i class="far fa-calendar-alt me-2"></i>
                        <h6 class="ml-2 mb-0" style="font-size: 14px;"><?php echo htmlspecialchars($date_group['display_date']); ?></h6>
                    </div>
                </div>
                
                <!-- Logs for this date -->
                <?php foreach($date_group['logs'] as $row): ?>
                    <div class="list-group-item list-group-item-action p-l-2 p-r-2">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1" style="gap: 4px">
                                    <div class="d-flex align-items-center">
                                        <div class="message-icon me-2">
                                            <img style="width: .975rem; height: .975rem;" src="../images/user.svg" alt="">
                                        </div>
                                        <h5 style="font-size: 11px" class="mb-0">
                                            <?php echo htmlspecialchars($row['logs']); ?>
                                        </h5>
                                    </div>
                                    <div class="small text-muted" style="justify-self: end; text-wrap:nowrap">
                                        <span><?php echo htmlspecialchars($row['logs_time']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-group-item text-center py-4">
                <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                <p class="mb-0">No activity</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="js/lib/sweetalert/sweetalert.min.js"></script>
<script src="js/lib/sweetalert/sweetalert.init.js"></script>

<?php 
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {

?>
    <script>
    sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
    </script>
<?php
unset($_SESSION['status']);
}
?>


</body>

</html>