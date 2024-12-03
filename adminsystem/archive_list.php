<?php

include '../connection/config.php';
$db = new Database();

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    
}


if(ISSET($_POST['add_research'])){

    $admin_id = $_SESSION['auth_user']['admin_id'];

    $project_title = $_POST['project_title'];
    $year = $_POST['year'];
    $department = $_POST['department'];
    $department_course = $_POST['department_course'];
    $abstract = $_POST['abstract'];
    $project_members = $_POST['project_members'];
    $owner_email = $_POST['owner_email'];

    $randomNumber = mt_rand(1000000000, 9999999999);

    date_default_timezone_set('Asia/Manila');
    $date = date('Y-m-d');


    $uploadDirectoryFILES = '../pdf_files/';


    $uniqueFilenamePDF = uniqid() . '-' . $_FILES['project_file']['name'];

    $pdfPath = $uploadDirectoryFILES . $uniqueFilenamePDF;

    if (move_uploaded_file($_FILES['project_file']['tmp_name'], $pdfPath)) {

        $sql = $db->insert_Archive_Research($randomNumber, $admin_id, $department, $department_course, $project_title, $date, $year, $abstract, $owner_email, $project_members, $pdfPath);
    
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You successfully submitted a Research Project.';

        $sql1 = $db->adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time);


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
    <title>Research Paper List: EARIST Research Archiving System</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo2.webp">
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
    <link href="../css/action-dropdown.css" rel="stylesheet">
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
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



<div class="content-wrap">
    <div class="main container-fluid">
        <div class="col-sm-12 col-md-12 col-xl-12 title-page">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-xl-12  flex justify-content-between align-items-center page-title">
                        <h1 style="display: flex; ">Research Papers</h1>
                        <div class="generate-report ">
                            <a target="_blank" href="generate_reports/generate_pdf.php?generate_report_for=all_research_papers" class="btn print-button">
                                Print
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Add research</h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data" onsubmit="prepareKeywords()">
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="row m-0" style="justify-content:space-between">
                                    <div class="col-sm-9 item-detail p-0">
                                        <label for="" class="info-label m-l-4">Research Title</label>
                                        <input type="text" class="info-input" name="project_title" minlength="8" required>
                                    </div>
                                    <div class="col-sm-2 item-detail p-0">
                                        <label for="" class="info-label m-l-4">Project year</label>
                                        <select class="info-input" style="" name="year" id="year" required>
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
                                <div class="row m-0" style="justify-content:space-between">
                                    <div class="col-sm-6 item-detail p-0">
                                        <label for="" class="info-label m-l-4">Department</label>
                                        <select class="info-input" name="department" id="department" required>
                                            <option value=""></option>
                                        <?php 
                                            $res = $db->showDepartments_WHERE_ACTIVE();

                                            foreach ($res as $item) {
                                            echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                                            }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-5 item-detail p-0">
                                        <label for="" class="info-label m-l-4">Course</label>
                                        <div class="course_dropdown">
                                            <select class="info-input" name="course" id="course" required>
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-detail">
                                    <label for="" class="info-label m-l-4">Researchers</label>
                                    <input type="text" class="info-input" name="project_members" required>
                                </div>
                                <div class="item-detail">
                                    <label for="" class="info-label m-l-4">Abstract</label>
                                    <textarea class="info-input" name="abstract" minlength="50" required></textarea>
                                </div>
                                <div class="item-detail">
                                    <label for="" class="info-label m-l-4">Keywords</label>
                                    <input type="text" class="info-input" id="keywords" name="keywords" required>
                                </div>
                                <div class="row m-0" style="justify-content:space-between">
                                    <div class="col-sm-6 item-detail p-0">
                                    <label for="" class="info-label m-l-4">Email address</label>
                                    <input type="email" class="info-input" id="owner_email" name="owner_email" required>
                                    </div>
                                    <div class="col-sm-5 item-detail p-0">
                                        <label for="" class="info-label m-l-4">Research Paper Softcopy (PDF)</label>
                                        <input type="file" id="pdfFile" accept=".pdf" class="info-input-file" style="border:none" name="project_file" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button name="add_research" class="btn btn-danger">Upload</button>
                        </div>
                        <div class="loading-overlay" style="display:none">
                            <div class="loading-content">
                                <div class="loading-spinner"></div>
                                <p class="progress-text">Uploading research paper...</p>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <section class="project-page-content">
            <div class="col-sm-12 col-md-12 col-xl-12">
                <div class="add-research">
                    <button type="button" class="add-research-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add research
                    </button>
                </div>
                <div class="list-container">
                    <table id="datatablesss" class="table list-table" style="width:100%">
                        <colgroup>
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                            <col style="width: 32%;">
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                            <col style="width: 8%;">
                            <col style="width: 10%;">
                            <col style="width: 6%;">
                        </colgroup>
                            <thead>
                                <tr>
                                    <th class="list-th">Date Created</th>
                                    <th class="list-th">Archive ID</th>
                                    <th class="list-th">Research Title</th>
                                    <th class="list-th">Department</th>
                                    <th class="list-th">Course</th>
                                    <th class="list-th">Plagiarized</th>
                                    <th class="list-th">Status</th>
                                    <th class="list-th"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                
                                
                                $data = $db->SELECT_ALL_ARCHIVE_RESEARCH();

                                foreach ($data as $result) {
                                ?>
                                <tr>
                                    <td class="list-td" 
                                        data-order="<?= (new DateTime($result['submission_date']))->format("Y-m-d") ?>">
                                        <?= (new DateTime($result['submission_date']))->format("d M Y") ?>
                                    </td>
                                    <td class="list-td"><?= $result['aid'] ?></td>
                                    <td class="list-td text-ellipsis"><?= $result['project_title'] ?></td>
                                    <td class="list-td"><?= $result['name'] ?></td>
                                    <td class="list-td"><?= $result['course_name'] ?></td>
                                    <td class="list-td">
                                        <?php 
                                            $plagiarized = $result['plagiarism_percentage'];
                                            $badgeColor = ($plagiarized === NULL) ? 'badge-success' : 'badge-danger';
                                        ?>
                                        <span class="badge <?= $badgeColor ?>" style="border-radius: 15px; font-size: 0.875rem">
                                            <?= $plagiarized === NULL ? 'No' : 'Yes' ?>
                                        </span>
                                    </td>
                                    <td class="list-td">
                                        <?php 
                                            $status = $result['document_status'];
                                            $badgeColor = ($status === 'Accepted') ? 'badge-success' : 'badge-danger';
                                        ?>
                                        <span class="badge <?= $badgeColor ?>" style="border-radius: 15px; font-size: 0.875rem">
                                            <?= $status == 'Accepted' ? 'Published' : $status?>
                                        </span>
                                    </td>
                                    
                                    <td class="list-td">
                                        <div class="action-container">
                                            <div>
                                                <button type="button" class="action-button"  id="action-button_<?= $result['archiveID'] ?>" aria-expanded="true" aria-haspopup="true">
                                                    Action
                                                    <svg class="action-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-slot="icon">
                                                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="dropdown-action" id="dropdown_<?= $result['archiveID'] ?>" role="action" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                <div role="none">
                                                <a href="view_archive_research.php?archiveID=<?= $result['aid'] ?>" class="dropdown-action-item">View paper</a>
                                                <a href="download_file.php?archiveID=<?= $result['aid'] ?>" class="dropdown-action-item" download>Download PDF</a>
                                                <?php 
                                                $document_status = $result['document_status'];
                                                if ($document_status === 'Accepted'){
                                                    echo '<a href="unpublish_research.php?archiveID='.$result['archiveID'].'" class="dropdown-action-item">Unpublish</a>';
                                                } elseif ($document_status === 'Not Accepted'){
                                                    echo '<a href="publish_research.php?archiveID='.$result['archiveID'].'" class="dropdown-action-item">Publish</a>';
                                                }
                                                ?>
                                                <a target="_blank" href="generate_reports/view_details.php?full_details=research_paper&archiveID=<?=  $result['aid'] ?>" class="dropdown-action-item">View receipt</a>
                                                <a href="#" onclick="confirmDelete(<?= $result['archiveID'] ?>)" class="dropdown-action-item">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <?php include 'templates/footer.php'; ?>
</div>


<script>
const keywordsInput = document.getElementById('keywords');
const tagify = new Tagify(keywordsInput, {
    delimiters: ",",
    maxTags: 7,   
    dropdown: {
        enabled: 0 
    }
});
function prepareKeywords() {
    document.getElementById('keywords').value = tagify.value.map(tag => tag.value).join(',');
}
const addModal = $('#modelId')
const form = document.querySelector('form');
const submitButton = document.querySelector('button[name="add_research"]');
const ulResult = document.getElementById('search-result');
const modalBackdrop = document.getElementsByClassName('modal-backdrop');
const loadingOverlay = document.querySelector('.loading-overlay');
const progressBar = document.querySelector('.progress-bar');
const progressText = document.querySelector('.progress-text');
const inputFile = document.getElementById('pdfFile');
form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (form.submitted) return;
    if (!validateForm()) return;

    const inputFile = document.getElementById('pdfFile');
    const file = inputFile.files[0];
    
    if (file && !validateFileSize(file, 20)) {
        return;
    }

    // Show loading overlay
    loadingOverlay.style.display = 'flex';
    submitButton.disabled = true;
    submitButton.textContent = 'Uploading...';

    const formData = new FormData(form);
    
    try {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_research.php', true);

        // Track upload progress
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 50;
                progressBar.style.width = percentComplete + '%';
                progressBar.setAttribute('aria-valuenow', percentComplete);
                progressText.textContent = `Uploading file... ${Math.round(percentComplete)}%`;
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                progressBar.style.width = '75%';
                progressBar.setAttribute('aria-valuenow', 75);
                progressText.textContent = 'Checking file content...';

                setTimeout(() => {
                    progressBar.style.width = '100%';
                    progressBar.setAttribute('aria-valuenow', 100);
                    progressText.textContent = 'Processing complete!';

                    setTimeout(() => {
                        const result = JSON.parse(xhr.responseText);
                        loadingOverlay.style.display = 'none';
                        
                        if (result.status === 'success') {
                            $(".sa-confirm-button-container button").attr("data-dismiss", "modal");
                            swal({
                                title: result.stats,
                                text: result.message,
                                type: result.status,
                                confirmButtonText: 'Okay',
                            }, function(isConfirm) {
                                if(isConfirm) {
                                    location.reload();
                                }
                            });
                        } else {
                            showError(result.message || 'Submission failed: Unknown error');
                        }
                        
                        // Reset progress bar
                        progressBar.style.width = '0%';
                        progressBar.setAttribute('aria-valuenow', 0);
                        progressText.textContent = 'Uploading research paper...';
                        
                    }, 500); // Delay before showing swal
                }, 1000); // Delay for final progress
            } else {
                showError('Server error occurred');
            }
        };

        xhr.onerror = function() {
            showError('Network error occurred');
        };

        xhr.send(formData);

    } catch (error) {
        showError(error || 'An error occurred during submission');
    }
});

function showError(message) {
    loadingOverlay.style.display = 'none';
    submitButton.disabled = false;
    submitButton.textContent = 'Save';
    swal({
        title: 'Error',
        text: message,
        type: 'error',
        confirmButtonText: 'Okay'
    });
}
function validateForm() {
    const projectTitle = document.querySelector('input[name="project_title"]');
    const abstract = document.querySelector('textarea[name="abstract"]');
    const fileInput = document.querySelector('input[name="project_file"]');
    
    if (!projectTitle.value || !abstract.value || !fileInput.files[0]) {
        alert('Please fill all required fields');
        return false;
    }
    
    if (fileInput.files[0].type !== 'application/pdf') {
        alert('Please upload a PDF file');
        return false;
    }
    
    return true;
}
function validateFileSize(file, maxSizeInMB) {
    const maxSizeInBytes = maxSizeInMB * 1024 * 1024;
    
    if (file.size > maxSizeInBytes) {
        swal({
            title: 'File Too Large',
            text: `Please upload a file smaller than ${maxSizeInMB} MB. 
                Current file size: ${(file.size / (1024 * 1024)).toFixed(2)} MB`,
            type: 'error',
            confirmButtonText: 'Okay'
        });
        return false;
    }
    return true;
}

new DataTable('#datatablesss', {
    order: [[0, 'desc']],
    columnDefs: [{
        targets: 0,
        type: 'date-eu'
    }]
});
$('#datatablesss_filter label input').removeClass('form-control form-control-sm');

function confirmDelete(archiveID){
    swal({
        title: "Are you sure you want to delete?",
        text: "You will not be able to recover this file!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#a33333",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm){
        if (isConfirm) {
            $.ajax({
                url: "delete_research.php",
                type: "GET",
                data: { archiveID: archiveID },
                success: function(response) {
                    swal({
                        title: "Deleted!",
                        text: "The research has been deleted.",
                        type: "success",
                        confirmButtonText: 'Okay',
                    }, 
                    function (isConfirm) {
                        if (isConfirm) {
                            // const listItem = document.getElementById(`li_${archiveID}`)
                            // if (listItem){
                            //     listItem.remove();
                            // }
                            location.reload();
                        }
                    });
                }
            });
        }
    });
}

function filteredData(){
    var department = $('#inputDepartment_search').val();
    var fromYear = $('#fromYear').val();
    var toYear = $('#toYear').val();
    var searchInput = $('#searchInput').val();
    var course = $('#department_course').val();
    $.ajax({
        url: 'fetch_filtered_projects.php',
        type: 'POST',
        dataType: 'json',
        data: {
            searchInput: searchInput,
            department: department,
            course: course,
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

$('#department_course, #fromYear, #toYear').change(filteredData);
$('#searchInput').on('keyup', function() {
    if ($(this).val().length > 0) { 
        filteredData();
    } else {
        filteredData();
        $('#data-result').hide();
    }
    $('#inputSearch').html($(this).val());
});

$("#department").change(function(){
    var department = $(this).val();

    if(department != " "){
    $.ajax({
      url:"show_course.php",
      method:"POST",
      data:{"send_department_set":1, "send_department":department},

      success:function(data){
        console.log(data);
        $("#course").html(data);
        $("#course").css("display","block");
      }
    });
  }else{
    $("#course").css("display","none");
  }
});
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