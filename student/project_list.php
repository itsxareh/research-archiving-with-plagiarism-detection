<?php
include '../connection/config.php';

$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['student_id']==0){
    echo"<script>window.location.href='login.php'</script>";
    
}

$searchInput = isset($_GET['searchInput']) ? $_GET['searchInput'] : '';
if ($searchInput) {
    $projects = $db->SELECT_OWNED_ARCHIVE_RESEARCH($_SESSION['auth_user']['student_email'], $searchInput, '', '', '', '');
    $displaySearchInfo = true;
} else {
    $projects = $db->SELECT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH();
    $displaySearchInfo = true;
}

if(ISSET($_POST['add_research'])){

    $student_id = $_SESSION['auth_user']['student_id'];
    $project_title = $_POST['project_title'];
    $year = $_POST['year'];
    $department = $_SESSION['auth_user']['department_id'];
    $department_course = $_SESSION['auth_user']['course_id'];
    $abstract = $_POST['abstract'];
    $keywords = $_POST['keywords'];
    $project_members = $_POST['project_members'];
    $owner_email = $_SESSION['auth_user']['student_email'];
    $randomNumber = rand(1000000000, 9999999999);

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');

    $uploadDirectoryFILES = '../pdf_files/'; 

    $uniqueFilenamePDF = uniqid() . '-' . $_FILES['project_file']['name'];


    $pdfPath = $uploadDirectoryFILES . $uniqueFilenamePDF;

    if (move_uploaded_file($_FILES['project_file']['tmp_name'], $pdfPath)) {
        $file = new CURLFile($pdfPath, 'application/pdf', $_FILES['project_file']['name']);
        //$sql = $db->insert_Archive_Research($randomNumber, $student_id, $department, $department_course, $project_title, $date, $year, $abstract, $owner_email, $project_members, $pdfPath);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:3000/upload_research");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => $file,
            'archive_id' => $randomNumber,
            'student_id' => $student_id,
            'project_title' => $project_title,
            'date_of_submit' => $date,
            'year' => $year,
            'department_id' => $department,
            'course_id' => $department_course,
            'abstract' => $abstract,
            'keywords' => $keywords,
            'project_members' => $project_members,
            'pdf_path' => $pdfPath,
            'owner_email' => $owner_email
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
    
        $responseData = json_decode($response, true);

        if ($responseData){
            date_default_timezone_set('Asia/Manila');
            $date = date('F / d l / Y');
            $time = date('g:i A');
            $logs = 'You successfully submitted your Research Paper';

            $sql1 = $db->student_Insert_NOTIFICATION($student_id, $logs, $date, $time);

            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "You successfully submitted your Research Paper";
            $_SESSION['status-code'] = "success";
        }
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
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title info-detail">Add New Research</h5>
                                    <button type="button" class="close p-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>
                            <form action="" method="POST" enctype="multipart/form-data" onsubmit="prepareKeywords()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row m-0" style="justify-content:space-between">
                                        <div class="col-sm-9 item-detail p-0">
                                            <label for="" class="info-label m-l-4">Research Title</label>
                                            <input type="text" class="info-input" name="project_title" minlength="20" placeholder="Enter Research Title..." required>
                                        </div>
                                        <div class="col-sm-2 item-detail p-0">
                                            <label for="" class="info-label m-l-4">Project year</label>
                                            <select class="info-input" style="height: auto !important;" name="year" id="year">
                                                <?php 
                                                    $defaultYear = date('Y');
                                                    for ($year = date('Y'); $year >= 1999; $year--) {
                                                        $selected = ($year == $defaultYear) ? 'selected' : ''; 
                                                        echo "<option value =\"$year\">$year</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Researchers</label>
                                        <input type="text" class="info-input" name="project_members" placeholder="Ex: John Doe, Peter Parker, Tony Stark" required>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Abstract</label>
                                        <textarea class="info-input" name="abstract" placeholder="Enter Project Abstract..." minlength="100" required></textarea>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Keywords</label>
                                        <input type="text" class="info-input" id="keywords" name="keywords" placeholder="Ex: Data Analytics, Machine Learning, Invention" required>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Project File (PDF)</label>
                                        <input type="file" accept=".pdf" class="info-input-file" name="project_file" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button name="add_research" class="btn btn-danger">Save</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
            <section class="project-page-content">
                <div class="col-md-3">
                    <div class="add-research">
                        <button type="button" class="add-research-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add New Research
                        </button>
                    </div>
                    <div class="advance-filter-search">
                        <p class="font-black bold">Filter</p>
                        <!-- <div class="mb-3 mb-sm-0">
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
                        </div> -->
                        <div class="mb-3">
                        <label class="item-meta" for="research_date">Sort by</label>
                            <select id="research_date" name="research_date" class="form-control item-meta" required>
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        <fieldset class="mb-3">
                            <div class="input-filter-group">
                                <label class="item-meta" for="info-label">Document Status:</label>
                                <select class="item-meta" style="width: auto;" name="documentStatus" id="documentStatus">
                                    <option value="">All</option>
                                    <option value="Accepted">Published</option>
                                    <option value="Not Accepted">Not yet published</option>
                                </select>
                        </fieldset>
                        <fieldset class="mb-3">
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
                        <div id="data-result" style="display:none">
                            <p><span id="resultNumber"><?= count($projects) ?></span> results for "<span id="inputSearch"><?= htmlspecialchars($searchInput) ?></span>"</span></p>
                        </div>
                    <?php endif; ?>

                    <ul id="search-result" tabindex="-1">
                    <?php
                        $student_email = $_SESSION['auth_user']['student_email'];
                        
                        $data = $db->SELECT_ALL_STUDENT_ARCHIVE_RESEARCH($student_email);
                        $i = 1;
                        foreach ($data as $result) {
                    ?>
                        <li class="project-list item" style="--i: <?=$i; $i++;?>;">
                            <div class="item-body">
                                <div class="project-tag">
                                    <?php 
                                        if ($result['document_status'] == "Accepted") {
                                            echo '<span class="badge badge-success tag" style="font-size: 12px;">Published</span>';
                                        } else {
                                                echo '<span class="badge badge-danger tag" style="font-size: 12px;">Not Published</span>';

                                        }
                                    ?>
                                    
                                </div>
                                <div class="item-title">
                                    <h3><a href="view_project_research.php?archiveID=<?= $result['archive_id'] ?>"><?php echo $result['project_title'];?></a></h3>
                                </div>
                                <div class="item-content">
                                    <p><?php echo $result['project_members'];?></p>
                                </div>
                                <div class="item-meta">
                                    <p><?php echo $result['name'];?></p>
                                    <p>Archive ID: <?php echo $result['archive_id'];?></p>
                                    <p>
                                        <?php 
                                            if (!empty($result['date_published'])) {
                                                $first_published = DateTime::createFromFormat("Y-m-d", $result['date_published'])->format("d F Y");
                                                echo "Published: ", $first_published;
                                            } else {
                                                 echo "Not yet published";
 
                                            }
                                        ?>
                                    </p>
                                </div>
                                <div class="item-abstract">
                                    <h3 class="abstract-title"><a href="#"><span>Abstract</span><i class="ti-angle-down f-s-10"></i></a></h3>
                                    <div class="abstract-group" style="display:none">
                                        <section class="item-meta">
                                            <div class="abstract-article">
                                                <p><?= $result['project_abstract']?></p>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                            <div class="project-action">
                                <a href="delete_research.php?archiveID=<?= $result['archiveID'] ?>" class="btn"><i class="ti-trash" title="Delete Research"></i></a>            
                            </div>
                        </li>
                    <?php
                    }
                    ?>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</div>
<script>
    $(".abstract-title").click(function(event){
        event.preventDefault();
        $(this).closest(".item-abstract").find(".abstract-group").slideToggle(200);
        $(this).find("i").toggleClass("ti-angle-down ti-angle-up"); 
    });

    const keywordsInput = document.getElementById('keywords');
    const tagify = new Tagify(keywordsInput, {
        delimiters: ",",
        maxTags: 5,   
        dropdown: {
            enabled: 0 
        }
    });

    function prepareKeywords() {
        document.getElementById('keywords').value = tagify.value.map(tag => tag.value).join(',');
    }

    function filteredData(){
        var owner_email = '<?= $_SESSION['auth_user']['student_email']?>';
        var documentStatus = $('#documentStatus').val();
        var fromYear = $('#fromYear').val();
        var toYear = $('#toYear').val();
        var searchInput = $('#searchInput').val();
        var research_date = $('#research_date').val();

        if (documentStatus === 'Not Accepted'){
            fromYear = '';
            toYear = '';
        }
        $.ajax({
            url: 'fetch_owned_projects.php',
            type: 'POST',
            dataType: 'json',   
            data: {
                owner_email: owner_email,
                searchInput: searchInput,
                documentStatus: documentStatus,
                fromYear: fromYear,
                toYear: toYear,
                research_date: research_date
            },
            success: function(response){
                console.log(response);
                console.log(response.html);
                console.log(response.count);
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
    
    $('#documentStatus, #research_date').change(filteredData);
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