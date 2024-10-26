<?php 
include '../connection/config.php';
$db = new Database();

$searchInput = $_POST['searchInput'];
$department = $_POST['department'];
$course = $_POST['course'];
$keywords = $_POST['keywords'];
$fromYear = $_POST['fromYear'];
$toYear = $_POST['toYear'];

$resp = $db->SELECT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear);

ob_start();

foreach ($resp as $row) {
    echo '<li>
            <div class="item-body">
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
        </li>';
}

$output = ob_get_clean();
echo json_encode([
    'count' => count($resp),
    'html' => $output
]);
exit;
?>
