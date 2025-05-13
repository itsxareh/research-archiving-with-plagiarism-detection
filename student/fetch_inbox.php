<?php 
include '../connection/config.php';
$db = new Database();


if ($_POST['searchInbox']){
    $searchInbox = $_POST['searchInbox'];
    $data = $db->SELECT_ACCOUNT_INBOX_WHERE($searchInbox);

    ob_start();
    if(!empty($data)){
        foreach($data as $row){
            echo '
            <div class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">    
                            <a href="view_digital_receipt.php?archiveID='.$row['archive_id'].'" target="_blank" rel="noopener noreferrer">
                                <h6 class="mb-0">
                                    '. htmlspecialchars($row['project_title']).'Digital Receipt
                                </h6>
                            </a>
                        </div>
                        <div class="small text-muted d-flex">
                            '. (new DateTime($row['dateOFSubmit']))->format("d F Y").'
                        </div>
                    </div>
                    <div class="message-actions">
                        <button class="btn btn-sm btn-link text-muted" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            ';
        }
    } else {
        echo '
        <div class="list-group-item text-center py-4">
            <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
            <p class="mb-0">No messages in your inbox</p>
        </div>
        ';
    }   
    $output = ob_get_clean();
    echo json_encode([$output]);
    exit();
}
?>