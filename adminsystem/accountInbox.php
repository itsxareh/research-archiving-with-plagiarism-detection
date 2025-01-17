<?php
if($_SESSION['auth_user']['admin_id']==0){
  echo"<script>window.location.href='index.php'</script>";
}

$inbox = $db->SELECT_ADMIN_INBOX();
?>
<strong>Inbox</strong><br>
    <div class="d-flex justify-content-end align-items-center mb-2">
        <div class="d-flex gap-2 inbox-container">
            <div class="input-group">
                <input id="searchInbox" type="text" class="form-control" placeholder="Search inbox...">
                <!-- <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button> -->
            </div>
            <!-- <button class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </button> -->
        </div>
    </div>

    <!-- Inbox Messages -->
    <div class="card p-0">
        <div class="list-group list-group-flush">
            <div id="search-result" style="max-height: 70svh; overflow-x: hidden">
            <?php if(!empty($inbox)): ?>
                <?php foreach($inbox as $row): ?>
                    <div class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-1">    
                                    <a href="view_archive_research.php?archiveID=<?= $row['archive_id'] ?>" target="_blank" <?= ($row['inbox_status']) == 'Unread' ? 'onclick="markInboxRead('.$row['archive_id'].')"' : '' ?>  rel="noopener noreferrer">
                                        <h6 class="mb-0">
                                            <?= htmlspecialchars($row['logs']) ?> 
                                        </h6>
                                    </a>
                                </div>
                                <div class="small text-muted d-flex">
                                    <?php echo htmlspecialchars($row['logs_date'])?>
                                </div>
                            </div>
                            <?php 
                                if ($row['inbox_status'] == 'Unread'){
                                    echo '
                                        <div id="markRead_'.$row['archive_id'].'">
                                            <img style="width: 20px; height: 20px" src="../images/dot.svg" alt="">
                                        </div>
                                        ';
                                }
                            ?>
                            <!-- <div class="message-actions">
                                
                                <button class="btn btn-sm btn-link text-muted" title="Delete">
                                    <img style="width: 15px; height: 15px" src="../images/trash.svg" alt="">
                                </button> 
                            </div> -->
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="list-group-item text-center py-4">
                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                    <p class="mb-0">No messages in your inbox</p>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<script src="js/lib/sweetalert/sweetalert.min.js"></script>
<script src="js/lib/sweetalert/sweetalert.init.js"></script>
<script>
    $('#searchInbox').on('keyup', function() {
        if ($(this).val().length > 0) { 
            filteredData();
        } else {
            filteredData();
        }
    });
    function filteredData() {
        var searchInbox =  $('#searchInbox').val();

        $.ajax({
            url: 'fetch_inbox.php',
            type: 'POST',
            dataType: 'json',
            data: {
                searchInbox: searchInbox,
            },
            success: function(response) {
                $('#search-result').html(response);
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
        });
    }
    function markInboxRead(archiveID) {
        $.ajax({
            url: 'mark_inbox_as_read.php',
            type: 'POST',
            dataType: 'json',
            data: {
                archiveID: archiveID,
            },
            success: function(response) {
                if (response.success) {
                    $(`#markRead_${archiveID}`).remove();
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr, status, error);
            }
        });
    }
</script>
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