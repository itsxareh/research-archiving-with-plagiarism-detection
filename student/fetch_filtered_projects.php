<?php 
include '../connection/config.php';
$db = new Database();

function buildQueryString($params) {
    $query = [];
    foreach ($params as $key => $value) {
        if (!empty($value)) {
            $query[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    return implode('&', $query);
}

// Gather filtering parameters
$searchInput = $_POST['searchInput'];
$department = $_POST['department'];
$course = $_POST['course'];
$keywords = $_POST['keywords'];
$fromYear = $_POST['fromYear'];
$toYear = $_POST['toYear'];
$research_date = $_POST['research_date'];
$page = isset($_POST['page']) ? $_POST['page'] : 1;
$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
$offset = ($page - 1) * $limit;

$params = [
    'searchInput' => $searchInput,
    'department' => $department,
    'course' => $course,
    'keywords' => $keywords,
    'fromYear' => $fromYear,
    'toYear' => $toYear,
    'research_date' => $research_date
];

$queryString = buildQueryString($params);


$totalFilteredCount = $db->COUNT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear, $research_date);
$resp = $db->SELECT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear, $research_date, $limit, $offset);

ob_start();

foreach ($resp as $row) {
    echo '<li>
            <div class="item-body">
                <div class="item-title">
                    <h3><a href="view_project_research.php?archiveID=' . $row['archive_id'] . '">' . ucwords($row['project_title']) . '</a></h3>
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
                    <h3 class="abstract-title"><a href="#"><span>Abstract</span><i class="ti-angle-down f-s-10"></i></a></h3>
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
$totalPages = ceil($totalFilteredCount / $limit);

echo '<div class="pagination-container">';
for ($i = 1; $i <= $totalPages; $i++) {
    $pageLink = '?page=' . $i . ($queryString ? '&' . $queryString : '');
    echo "<a class='pagination' onclick='filteredData()' href='$pageLink'" 
        . ($i == $page ? ' id="active"' : '') . ">$i</a>";
}
echo '</div>';


$output = ob_get_clean();

echo json_encode([
    'count' => count($resp),
    'totalFilteredCount' => $totalFilteredCount,
    'html' => $output,
    'totalPages' => $totalPages,
    'currentPage' => $page
]);
exit;
?>
