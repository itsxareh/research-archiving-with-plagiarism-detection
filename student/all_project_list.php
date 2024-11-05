<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$searchInput = isset($_GET['searchInput']) ? $_GET['searchInput'] : '';
$department = isset($_GET['department']) ? $_GET['department'] : '';
$course = isset($_GET['course']) ? $_GET['course'] : '';
$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
$fromYear = isset($_GET['fromYear']) ? $_GET['fromYear'] : '';
$toYear = isset($_GET['toYear']) ? $_GET['toYear'] : '';
$research_date = isset($_GET['research_date']) ? $_GET['research_date'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_POST['limit']) ? $_POST['limit'] : 10;
$offset = ($page - 1) * $limit;

if ($searchInput || $department || $course || $keywords || $fromYear || $toYear || $research_date) {
    $totalFilteredProjects = $db->COUNT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear, $research_date);
    $totalPages = ceil($totalFilteredProjects / $limit);

    $projects = $db->SELECT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear, $research_date, $limit, $offset);
    $displaySearchInfo = true;
} else {
    $totalProjects = count($db->SELECT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH(PHP_INT_MAX, 0));
    $totalPages = ceil($totalProjects / $limit);

    $projects = $db->SELECT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH($limit, $offset);
    $displaySearchInfo = true;
}

if(ISSET($_POST['add_research'])){

    $student_id = $_SESSION['auth_user']['student_id'];

    $project_title = $_POST['project_title'];
    $year = $_POST['year'];
    $department = $_SESSION['auth_user']['department_id'];
    $department_course = $_SESSION['auth_user']['course_id'];
    $abstract = $_POST['abstract'];
    $project_members = $_POST['project_members'];
    $owner_email = $_SESSION['auth_user']['student_email'];

    $randomNumber = mt_rand(1000000000, 9999999999);

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');

    $uploadDirectory = '../imageFiles/'; 

    $uploadDirectoryFILES = '../pdf_files/';

    $uniqueFilenamepicture = uniqid() . '-' . $_FILES['thumbnail']['name'];

    $uniqueFilenamePDF = uniqid() . '-' . $_FILES['project_file']['name'];

    $imagePath = $uploadDirectory . $uniqueFilenamepicture;

    $pdfPath = $uploadDirectoryFILES . $uniqueFilenamePDF;

    if (move_uploaded_file($_FILES['project_file']['tmp_name'], $pdfPath) && move_uploaded_file($_FILES['thumbnail']['tmp_name'], $imagePath)) {

        $sql = $db->insert_Archive_Research($randomNumber, $department, $department_course, $project_title, $date, $year, $abstract, $owner_email, $project_members, $imagePath, $pdfPath);
    
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully submitted your Research Paper';

        $sql1 = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);


        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Research Added Successfully";
        $_SESSION['status-code'] = "success";
    } else {
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Failed to move image file.";
        $_SESSION['status-code'] = "error";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- theme meta -->
    <meta name="theme-name" content="focus" />
    <title>Project List: EARIST Research Archiving System</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo1.png">
    <!-- Retina iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="144x144" href="http://placehold.it/144.png/000/fff">
    <!-- Retina iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="114x114" href="http://placehold.it/114.png/000/fff">
    <!-- Standard iPad Touch Icon-->
    <link rel="apple-touch-icon" sizes="72x72" href="http://placehold.it/72.png/000/fff">
    <!-- Standard iPhone Touch Icon-->
    <link rel="apple-touch-icon" sizes="57x57" href="http://placehold.it/57.png/000/fff">
    <!-- Styles -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/owl.carousel.min.css" rel="stylesheet" />
    <link href="css/lib/owl.theme.default.min.css" rel="stylesheet" />
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">


    <!---------------------DATATABLES------------------------->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>


   

</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/student_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



<div class="content-wrap">
    <div class="container" >
        <div class="col-md-12" >
            <div class="row" >
                <div class="col-md-12 ">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Research Papers</h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="project-page-content row" >
                <div class="col-sm-12 col-md-3 ">
                    <div class="advance-filter-search">
                        <p class="font-black bold">Filter</p>
                        <div class="mb-3">
                            <label class="item-meta" for="research_date">Sort by</label>
                            <select id="research_date" name="research_date" class="form-control item-meta" required>
                                <option value=""></option>
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="" class="item-meta">Select Department</label>
                            <select id="inputDepartment_search" name="department" class="selectpicker form-control item-meta" onchange="filteredData()" required>
                            <option value="">All</option>
                            <?php 
                                $res = $db->showDepartments_WHERE_ACTIVE();
                                
                                foreach ($res as $item) {
                                    if (isset($department)=== $item['id']){
                                        echo '<option value="'.$item['id'].' selected ">'.$item['name'].'</option>';
                                    }
                                    echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                                } 
                            ?>
                            
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="item-meta" for="course">Course</label>
                            <select id="department_course" name="department_course" class="selectpicker form-control" required> 
                            <option value=""></option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <fieldset>
                                <div class="input-filter-group">
                                    <label class="item-meta" for="info-label">From:</label>
                                    <select class="item-meta" name="fromYear" id="fromYear">
                                        <option></option>
                                        <?php 
                                            $defaultYear = 1999;
                                            for ($year = 1999; $year <= 2024; $year++) {
                                                $selected = ($year == $defaultYear) ? 'selected' : ''; 
                                                echo "<option value =\"$year\">$year</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="input-filter-group">
                                    <label class="item-meta" for="info-label">To:</label>
                                    <select class="item-meta" name="toYear" id="toYear">
                                        <option value=""></option>
                                        <?php 
                                            $defaultYear = date('Y');
                                            for ($year = date('Y'); $year >= 1999; $year--) {
                                                $selected = ($year == $defaultYear) ? 'selected' : ''; 
                                                echo "<option value =\"$year\">$year</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                        <div class="mb-3">
                            <label class="item-meta" for="keywords">Keywords</label>
                            <input id="keywords" name="keywords" class="form-control-keyword" value="" required> 
                        </div>
                    </div>
                </div>
                <div class="col-md-9 project-list-container">
                    <div id="data-result" style="display: none;">
                    <?php if ($displaySearchInfo): ?>
                        <p><span id="resultNumber"></span> results found <span id="inputSearch" style="display: none; font-weight:400"></span></p>
                    </div>
                    <?php endif; ?>
                    <ul id="search-result" >
                        <?php $i=1; foreach ($projects as $result){ ?>
                            <li class="item" style="--i: <?=$i?>;">
                                <div class="item-body" >
                                    <div class="item-title">
                                        <h4><a href="view_project_research.php?archiveID=<?= $result['archive_id'] ?>"><?= ucwords($result['project_title']); ?></a></h4>
                                    </div>
                                    <div class="item-content">
                                        <p><?= $result['project_members']; ?></p>
                                    </div>
                                    <div class="item-meta">
                                        <p><?= $result['name']; ?></p>
                                        <p>Archive ID: <?= $result['archive_id']; ?></p>
                                        <?php if (!empty($result['date_published'])): ?>
                                            <p>Published: <?= DateTime::createFromFormat("Y-m-d", $result['date_published'])->format("d F Y"); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-abstract">
                                        <h3 class="abstract-title"><a href="#"><span>Abstract</span><img src="../images/arrow-down.svg" style="width: .675rem; height: .675rem" alt=""></a></h3>
                                        <div class="abstract-group" style="display:none">
                                            <section class="item-meta">
                                                <div class="abstract-article">
                                                    <p><?= $result['project_abstract']; ?></p>
                                                </div>
                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php $i++; } ?>
                        <div class="pagination-container">
                            <?php
                                $params = [
                                    'department' => isset($_GET['department']) ? $_GET['department'] : '',
                                    'fromYear' => isset($_GET['fromYear']) ? $_GET['fromYear'] : '',
                                    'toYear' => isset($_GET['toYear']) ? $_GET['toYear'] : '',
                                    'research_date' => isset($_GET['research_date']) ? $_GET['research_date'] : '',
                                    'searchInput' => isset($_GET['searchInput']) ? $_GET['searchInput'] : '',
                                    'course' => isset($_GET['course']) ? $_GET['course'] : '',
                                    'keywords' => isset($_GET['keywords']) ? $_GET['keywords'] : ''
                                ];

                                // Filter out empty parameters
                                $queryString = http_build_query(array_filter($params));

                                $pageUrl = function($pageNum) use ($queryString) {
                                    return "?page=$pageNum" . ($queryString ? "&$queryString" : '');
                                };

                                $visiblePages = 5; 
                                $startPage = max(1, $page - floor($visiblePages / 2));
                                $endPage = min($totalPages, $startPage + $visiblePages - 1);
                        
                                // Adjust startPage if near the end of pagination range
                                if ($endPage - $startPage + 1 < $visiblePages) {
                                    $startPage = max(1, $endPage - $visiblePages + 1);
                                }
                        
                                // "Prev" button
                                if ($page > 1) {
                                    echo '<a class="pagination prev" href="' . $pageUrl($page - 1) . '" onclick="filteredData()">Prev</a>';
                                }
                        
                                // First page link
                                if ($startPage > 1) {
                                    echo '<a class="pagination" href="' . $pageUrl(1) . '" onclick="filteredData()">1</a>';
                                    if ($startPage > 2) {
                                        echo '<span>...</span>';
                                    }
                                }
                        
                                // Display page numbers within the range
                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    echo '<a class="pagination" href="' . $pageUrl($i) . '" onclick="filteredData()" ' .
                                        ($i == $page ? 'id="active"' : '') . '>' . $i . '</a>';
                                }
                        
                                // Last page link
                                if ($endPage < $totalPages) {
                                    if ($endPage < $totalPages - 1) {
                                        echo '<span>...</span>';
                                    }
                                    echo '<a class="pagination" href="' . $pageUrl($totalPages) . '" onclick="filteredData()">' . $totalPages . '</a>';
                                }
                        
                                // "Next" button
                                if ($page < $totalPages) {
                                    echo '<a class="pagination next" href="' . $pageUrl($page + 1) . '" onclick="filteredData()">Next</a>';
                                } 
                                ?>
                        </div>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</div>


<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
<script>

    window.onpopstate = function(event) {
        filteredData();
    }; 
    $('#search-result').on('click', 'h3.abstract-title', function(event){
        console.log('clicked');
        event.preventDefault();
        $(this).closest('.item-abstract').find('.abstract-group').slideToggle(200);
        
        const img = $(this).find('img');
        const isArrowDown = img.attr('src').includes('arrow-down');
        img.attr('src', isArrowDown ? '../images/arrow-up.svg' : '../images/arrow-down.svg');
    });

    const keywordsInput = document.getElementById('keywords');
    const tagify = new Tagify(keywordsInput, {
        delimiters: ",",
        maxTags: 7,   
        dropdown: {
            enabled: 0 
        }
    });
    function getKeywords() {
        return tagify.value.map(tag => tag.value).join(',');
    }

    function getURLParameter(name) {
        return new URLSearchParams(window.location.search).get(name);
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Get parameters from the URL
        const searchInput = getURLParameter('searchInput');
        const researchDate = getURLParameter('research_date');
        const department = getURLParameter('department');
        const course = getURLParameter('department_course');
        const fromYear = getURLParameter('fromYear');
        const toYear = getURLParameter('toYear');
        const keywords = getURLParameter('keywords');

        // Set the values of inputs or selects based on the URL parameters
        if (searchInput) document.getElementById('searchInput').value = searchInput;
        if (researchDate) document.getElementById('research_date').value = researchDate;
        if (department) document.getElementById('inputDepartment_search').value = department;
        if (course) document.getElementById('department_course').value = course;
        if (fromYear) document.getElementById('fromYear').value = fromYear;
        if (toYear) document.getElementById('toYear').value = toYear;
        if (keywords) document.getElementById('keywords').value = keywords;
    });
    function filteredData() {
        var department =  $('#inputDepartment_search').val();
        var fromYear =  $('#fromYear').val();
        var toYear =  $('#toYear').val();
        var research_date =  $('#research_date').val();
        var searchInput =  $('#searchInput').val();
        var course = (department === '') ? '' : $('#department_course').val() ;
        var keywords = getKeywords(); 
        var page = 1 ;
        var limit = <?= isset($limit) ? $limit : 10 ?>;


    $.ajax({
        url: 'fetch_filtered_projects.php',
        type: 'POST',
        dataType: 'json',
        data: {
            searchInput: searchInput,
            department: department,
            course: course,
            keywords: keywords,
            fromYear: fromYear,
            toYear: toYear,
            research_date: research_date,
            page: page,
            limit: limit
        },
        success: function(response) {
            $('#search-result').html(response.html);
            $('#resultNumber').text(response.totalFilteredCount); 
            if(response.count > 0) {
                $('#search-result').html(response.html);
            } else {
                $('#search-result').html("<p class='text-center' style='color: #666; font-size: 14px; font-weight:400'>No projects found.</p>");
            }
            if (searchInput.length > 0) {
                $('#inputSearch').show();
                $('#inputSearch').html('for "<strong>'+searchInput+'</strong>"');
            } else {
                $('#inputSearch').hide();
            }
            if (response.count > 0 || searchInput.length > 0) {
                $('#resultNumber').html(response.totalFilteredCount);
                $('#data-result').show();
            } else {
                $('#data-result').hide();
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr, status, error);
        }
    });
}
    tagify.on('add', filteredData);
    tagify.on('remove', filteredData);

    $('#department_course, #research_date, #fromYear, #toYear').change(filteredData);
    $('#searchInput').on('keyup', function() {
        if ($(this).val().length > 0) { 
            filteredData();
        } else {
            filteredData();
            $('#data-result').hide();
        }
        $('#inputSearch').html($(this).val());
    });

    $("#inputDepartment_search").change(function(){
    var department = $(this).val();
    filteredData();
    if(department != " "){
            $.ajax({
                url:"show_course.php",
                method:"POST",
                data:{
                    "send_department_set":1, 
                    "send_department":department

                },
                success:function(data){
                if (data.trim() !== ""){
                    $("#department_course").html(data);
                    $("#department_course").css("display","block");
                } else {
                    $("#department_course").html('<option value="">No course found</option>');
                    $("#department_course").css("display","block");
                }
                },
                error: function(xhr, status, error){
                    console.log("AJAX Error: ", xhr);
                    console.log("AJAX Error: ", status);
                    console.log("AJAX Error: ", error);
                }
            });
    }else{
        filteredData();
    }
    });
</script>

    <!-- Common -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>

    <!-- Sweet Alert -->
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