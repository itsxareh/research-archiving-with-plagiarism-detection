<?php 
include '../connection/config.php';
$db = new Database();

$owner_email = $_POST['owner_email'];
$searchInput = $_POST['searchInput'];
$documentStatus = $_POST['documentStatus'];
$fromYear = $_POST['fromYear'];
$toYear = $_POST['toYear'];
$research_date = $_POST['research_date'];

$resp = $db->SELECT_OWNED_ARCHIVE_RESEARCH($owner_email, $searchInput, $fromYear, $toYear, $documentStatus, $research_date);

ob_start();

foreach ($resp as $row) {
    echo '<li class="project-list">
            <div class="item-body">
                <div class="project-tag">';
                if ($row['document_status'] == "Accepted") {
                    echo '<span class="badge badge-success tag" style="font-size: 12px;">Published</span>';
                } else {
                        echo '<span class="badge badge-danger tag" style="font-size: 12px;">Not Published</span>';

                }
            echo '</div>
                <div class="item-title">
                    <h3><a href="view_project_research.php?archiveID=' . $row['archive_id'] . '">' . $row['project_title'] . '</a></h3>
                </div>
                <div class="item-content">
                    <p>' . $row['project_members'] . '</p>
                </div>
                <div class="item-meta">
                    <p>' . $row['name'] . '</p>
                    <p>Archive ID: ' . $row['archive_id'] . '</p>';

    if (!empty($row['date_published'])) {
        $first_published = DateTime::createFromFormat("Y-m-d", $row['date_published'])->format("d F Y");
        echo '<p>Published: ' . $first_published . '</p>';
    } else {
        echo "Not yet published";

   }

    echo '      </div>
                <div class="item-abstract">
                    <h3><a href="#"><span>Abstract</span><i class="ti-angle-down f-s-10"></i></a></h3>
                    <div class="abstract-group" style="display:none">
                        <section class="item-meta">
                            <div class="abstract-article">
                                <p>' . $row['project_abstract'] . '</p>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <div class="project-action">
                <a class="btn" href="delete_research.php?archiveID='.$row['archiveID'].'"><i class="ti-trash" title="Delete Research"></i></a>            
            </div>
        </li>';
}

$output = ob_get_clean();
echo json_encode([
    'count' => count($resp),
    'html' => $output
]);
exit;
?>
