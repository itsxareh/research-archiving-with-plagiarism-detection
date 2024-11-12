<?php

include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// if($_SESSION['auth_user']['student_id']==0){
//     echo"<script>window.location.href='login.php'</script>";
    
// }

$searchInput = isset($_GET['searchInput']) ? $_GET['searchInput'] : '';
if ($searchInput) {
    $projects = $db->SELECT_FILTERED_ARCHIVE_RESEARCH($searchInput, '', '', '');
    $displaySearchInfo = true;
} else {
    $projects = $db->SELECT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH();
    $displaySearchInfo = false;
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
        $logs = 'You successfully submitted your research paper.';

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
    <link href="css/lib/themify-icons.css" rel="stylesheet">
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
    <div class="container">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Research Papers</h1>
                        </div>
                    </div>
                </div>
            </div>
            <section class="project-page-content">
                <div class="col-md-3">
                    <div class="advance-filter-search">
                        <p class="font-black bold">Filter</p>
                        <div class="mb-3 mb-sm-0">
                            <label for="" class="item-meta">Select Department</label>
                            <select id="inputDepartment_search" name="department" class="selectpicker form-control item-meta" required>
                            <option></option>
                            <?php 
                                $res = $db->showDepartments_WHERE_ACTIVE();

                                foreach ($res as $item) {
                                echo '<option value="'.$item['name'].'">'.$item['name'].'</option>';
                                }
                            ?>
                            
                            </select>
                        </div>
                        <fieldset>
                            <div class="input-filter-group">
                                <label class="item-meta" for="info-label">From:</label>
                                <select class="item-meta" name="fromYear" id="fromYear">
                                    <option value=""></option>
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
                </div>
                <div class="col-md-9">
                    <?php if ($displaySearchInfo): ?>
                        <div id="data-result">
                            <p><span id="resultNumber"><?= count($projects) ?></span> results for "<span id="inputSearch"><?= htmlspecialchars($searchInput) ?></span>"</span></p>
                        </div>
                    <?php endif; ?>
                    
                    <ul id="search-result">
                        <?php foreach ($projects as $result): ?>
                            <li>
                                <div class="item-body">
                                    <div class="item-title">
                                        <h3><a href="view_project_research.php?archiveID=<?= $result['archive_id'] ?>"><?= $result['project_title']; ?></a></h3>
                                    </div>
                                    <div class="item-content">
                                        <p><?= $result['project_members']; ?></p>
                                    </div>
                                    <div class="item-meta">
                                        <p><?= $result['name']; ?></p>
                                        <p>Archive ID: <?= $result['archive_id']; ?></p>
                                        <?php if (!empty($result['date_published'])): ?>
                                            <p>First Published: <?= DateTime::createFromFormat("Y-m-d", $result['date_published'])->format("d F Y"); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-abstract">
                                        <h3><a href="#"><span>Abstract</span><i class="ti-angle-down f-s-10"></i></a></h3>
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
                        <?php endforeach; ?>
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
    $(".item-abstract h3 a").click(function(event){
        event.preventDefault();
        $(this).closest(".item-abstract").find(".abstract-group").slideToggle(200);
        $(this).find("i").toggleClass("ti-angle-down ti-angle-up"); 
    });

    function filteredData(){
        var department = $('#inputDepartment_search').val();
        var fromYear = $('#fromYear').val();
        var toYear = $('#toYear').val();
        var searchInput = $('#searchInput').val();
        $.ajax({
            url: 'fetch_filtered_projects.php',
            type: 'POST',
            dataType: 'json',
            data: {
                searchInput: searchInput,
                department: department,
                fromYear: fromYear,
                toYear: toYear
            },
            success: function(response){
                $('#search-result').html(response.html);
                $('#resultNumber').text(response.count);

                if (searchInput.length > 0 && response.count > 0 || searchInput.length > 0){
                    $('#data-result').show();
                } else {
                    $('#data-result').hide();
                }
            },
            error: function(xhr, status, error){
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    }
    
    $('#inputDepartment_search').change(filteredData);
    $('#fromYear, #toYear').change(filteredData);
    $('#searchInput').on('keyup', function() {
        if ($(this).val().length > 0) { 
            filteredData();
        } else {
            filteredData();
            $('#data-result').hide();
        }
        $('#inputSearch').html($(this).val());
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