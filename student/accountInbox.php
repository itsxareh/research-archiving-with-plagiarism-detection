<?php
if($_SESSION['auth_user']['student_id']==0){
  echo"<script>window.location.href='login.php'</script>";
}

$inbox = $db->SELECT_ACCOUNT_INBOX($student_id);
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
                                    <a href="view_digital_receipt.php?archiveID=<?= $row['archive_id'] ?>" target="_blank" rel="noopener noreferrer">
                                        <h6 class="mb-0">
                                            <?= htmlspecialchars($row['project_title']) ?> Digital Receipt
                                        </h6>
                                    </a>
                                </div>
                                <div class="small text-muted d-flex">
                                    <?php echo (new DateTime($row['dateOFSubmit']))->format("d F Y")?>
                                </div>
                            </div>
                            <div class="message-actions">
                                <button class="btn btn-sm btn-link text-muted" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
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
        console.log($('#searchInbox').val());
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
                console.log(response);
                $('#search-result').html(response);
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