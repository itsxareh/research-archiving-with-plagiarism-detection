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

$owner_email = $_POST['owner_email'];
$searchInput = $_POST['searchInput'];
$documentStatus = $_POST['documentStatus'];
$keywords = $_POST['keywords'];
$fromYear = $_POST['fromYear'];
$toYear = $_POST['toYear'];
$research_date = $_POST['research_date'];
$page = isset($_POST['page']) ? $_POST['page'] : 1;
$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
$offset = ($page - 1) * $limit;

$params = [
    'searchInput' => $searchInput,
    'documentStatus' => $documentStatus,
    'keywords' => $keywords,
    'fromYear' => $fromYear,
    'toYear' => $toYear,
    'research_date' => $research_date
];

$queryString = buildQueryString($params);


$totalFilteredCount = $db->COUNT_FILTERED_OWNED_ARCHIVE_RESEARCH($owner_email, $searchInput, $keywords, $documentStatus, $fromYear, $toYear, $research_date);
$resp = $db->SELECT_OWNED_ARCHIVE_RESEARCH($owner_email, $searchInput, $keywords, $fromYear, $toYear, $documentStatus, $research_date, $limit, $offset);

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
                    <h4><a href="view_project_research.php?archiveID=' . $row['archive_id'] . '">' . $row['project_title'] . '</a></h4>
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
            <div class="project-action">
                <a class="btn" href="delete_research.php?archiveID='.$row['archiveID'].'"><i class="ti-trash" title="Delete Research"></i></a>            
            </div>
        </li>';
}
$totalPages = ceil($totalFilteredCount / $limit);

echo '<div class="pagination-container">';

$visiblePages = 5; // Number of pages to show around the current page
$startPage = max(1, $page - floor($visiblePages / 2));
$endPage = min($totalPages, $startPage + $visiblePages - 1);

// Adjust startPage if near the end of pagination range
if ($endPage - $startPage + 1 < $visiblePages) {
    $startPage = max(1, $endPage - $visiblePages + 1);
}

// "Prev" button
if ($page > 1) {
    $prevPageLink = '?page=' . ($page - 1) . ($queryString ? '&' . $queryString : '');
    echo "<a class='pagination prev' onclick='filteredData()' href='$prevPageLink'>Prev</a>";
}

// First page link
if ($startPage > 1) {
    $firstPageLink = '?page=1' . ($queryString ? '&' . $queryString : '');
    echo "<a class='pagination' onclick='filteredData()' href='$firstPageLink'>1</a>";
    if ($startPage > 2) {
        echo "<span>...</span>";
    }
}

// Display page numbers within the range
for ($i = $startPage; $i <= $endPage; $i++) {
    $pageLink = '?page=' . $i . ($queryString ? '&' . $queryString : '');
    echo "<a class='pagination' onclick='filteredData()' href='$pageLink'" 
        . ($i == $page ? ' id="active"' : '') . ">$i</a>";
}

// Last page link
if ($endPage < $totalPages) {
    if ($endPage < $totalPages - 1) {
        echo "<span>...</span>";
    }
    $lastPageLink = '?page=' . $totalPages . ($queryString ? '&' . $queryString : '');
    echo "<a class='pagination' onclick='filteredData()' href='$lastPageLink'>$totalPages</a>";
}

// "Next" button
if ($page < $totalPages) {
    $nextPageLink = '?page=' . ($page + 1) . ($queryString ? '&' . $queryString : '');
    echo "<a class='pagination next' onclick='filteredData()' href='$nextPageLink'>Next</a>";
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
