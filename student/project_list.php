<?php
include '../connection/config.php';

$db = new Database();

session_start();
if($_SESSION['auth_user']['student_id']==0){
    echo"<script>window.location.href='login.php'</script>";
    
}

$student_email = isset($_SESSION['auth_user']['student_email']) ? $_SESSION['auth_user']['student_email'] : '';

$searchInput = isset($_GET['searchInput']) ? $_GET['searchInput'] : '';
$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
$fromYear = isset($_GET['fromYear']) ? $_GET['fromYear'] : '';
$toYear = isset($_GET['toYear']) ? $_GET['toYear'] : '';
$document_status = isset($_GET['documentStatus']) ? $_GET['documentStatus'] : '';
$research_date = isset($_GET['research_date']) ? $_GET['research_date'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_POST['limit']) ? $_POST['limit'] : 20;
$offset = ($page - 1) * $limit;

if ($searchInput || $keywords || $fromYear || $toYear || $research_date) {
    $totalFilteredProjects = $db->COUNT_FILTERED_OWNED_ARCHIVE_RESEARCH($student_email, $searchInput, $keywords, $document_status, $fromYear, $toYear, $research_date);
    $totalPages = ceil($totalFilteredProjects / $limit);

    $projects = $db->SELECT_OWNED_ARCHIVE_RESEARCH($student_email, $searchInput, $keywords, $fromYear, $toYear, $document_status, $research_date, $limit, $offset);
    $displaySearchInfo = true;
} else {
    $totalProjects = count($db->SELECT_ALL_OWNED_ARCHIVE_RESEARCH($student_email, PHP_INT_MAX, 0));
    $totalPages = ceil($totalProjects / $limit);

    $projects = $db->SELECT_ALL_STUDENT_ARCHIVE_RESEARCH($student_email, $limit, $offset);
    $displaySearchInfo = true;
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
    <title>Project List: EARIST Repository</title>
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
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">


    <!---------------------DATATABLES------------------------->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css" rel="stylesheet">

    
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
                                <h5 class="modal-title info-detail" style="font-size: 12px">Add research</h5>
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
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Collaborators (Email Address)</label>
                                        <input type="text" class="info-input" id="project_members" name="project_members" required>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Abstract</label>
                                        <textarea class="info-input" name="abstract" minlength="50" required></textarea>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Keywords</label>
                                        <input type="text" class="info-input" id="keywords" name="keywords" required>
                                    </div>
                                    <div class="item-detail">
                                        <label for="" class="info-label m-l-4">Research Paper Softcopy (PDF)</label>
                                        <input type="file" id="pdfFile" accept=".pdf, .doc, .docx" class="info-input-file" style="border:none" name="project_file" required>
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
            <section class="project-page-content row">
                <div class="col-sm-12 col-md-3 col-xl-3">
                    <div class="add-research">
                        <button type="button" class="add-research-button item-meta" data-toggle="modal" data-target="#modelId">
                         <img style="width: 15px; height: 15px "  src="../images/plus.svg">
                         Create
                        </button>
                    </div>
                    <div class="advance-filter-search">
                        <div class="d-flex align-items-end justify-content-between">
                            <p class="font-black bold m-0">Filter</p>
                            <a href="#" class="clear-filter-button" onclick="clearFilter()">Clear</a>
                        </div>
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
                                <option value=""></option>    
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        <fieldset class="mb-3">
                            <label class="item-meta" for="info-label">Document Status:</label>
                            <select class="form-control item-meta" name="documentStatus" id="documentStatus">
                                <option value="">All</option>
                                <option value="Accepted">Published</option>
                                <option value="Rejected">Rejected</option>
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
                        <div class="mb-3">
                            <label class="item-meta" for="keywords">Keywords</label>
                            <input id="filter-keywords" name="keywords" class="form-control-keyword" value="" required />
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div id="data-result" style="display:none">
                        <?php if ($displaySearchInfo): ?>
                        <p><span id="resultNumber"></span> results found <span id="inputSearch" style="display: none; font-weight:400"></span></p>
                    </div>
                    <?php endif; ?>
                    <ul id="search-result" tabindex="-1">
                    <?php
                        $i = 1;
                        if (count($projects) > 0) {
                            foreach ($projects as $result) {
                    ?>
                        <li class="project-list item" id="li_<?= $result['archiveID'] ?>" style="--i: <?=$i;?>;">
                            <div class="item-body">
                                <div class="project-tag">
                                    <?php 
                                        if ($result['document_status'] == "Accepted") {
                                            echo '<span class="badge badge-success tag" style="font-size: 12px;">Published</span>';
                                        } else {
                                                echo '<span class="badge badge-danger tag" style="font-size: 12px;">'.$result['document_status'].'</span>';

                                        }
                                    ?>
                                    
                                </div>
                                <div class="item-title">
                                    <h4><a href="view_project_research.php?archiveID=<?= $result['archive_id'] ?>"><?php echo ucwords($result['project_title']);?></a></h4>
                                </div>
                                <div class="item-content">
                                    <p><?php echo implode(', ', array_map('trim', explode(',', $result['project_members'])));?></p>
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
                                    <h3 class="abstract-title"><a href="#"><span>Abstract</span><img src="../images/arrow-down.svg" style="width: .675rem; height: .675rem" alt=""></a></h3>
                                    <div class="abstract-group" style="display:none; cursor: pointer;">
                                        <section class="item-meta">
                                            <div class="abstract-article">
                                                <p><?= $result['project_abstract']?></p>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                            <?php if (trim($result['research_owner_email']) == $_SESSION['auth_user']['student_email']): ?>
                            <div class="project-action">
                                <button  onclick="confirmDelete(<?= $result['archiveID'] ?>)" class="btn"><img style="width: 20px; height: 20px" src="images/svg/delete.svg" title="Delete Research"></img></a>            
                            </div>
                            <?php endif; ?>
                        </li>
                        <?php
                            $i++;
                            }
                        } else {
                            echo "<p style='text-align: center'>No uploaded research found.</p>";
                        }
                        ?>
                        <div class="pagination-container">
                            <?php
                                $params = [
                                    'documentStatus' => isset($_GET['documentStatus']) ? $_GET['documentStatus'] : '',
                                    'fromYear' => isset($_GET['fromYear']) ? $_GET['fromYear'] : '',
                                    'toYear' => isset($_GET['toYear']) ? $_GET['toYear'] : '',
                                    'research_date' => isset($_GET['research_date']) ? $_GET['research_date'] : '',
                                    'searchInput' => isset($_GET['searchInput']) ? $_GET['searchInput'] : '',
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
    <?php include 'templates/footer.php'; ?>
</div>

<script>

$('#fromYear, #toYear').on('change', function() {
    if ($('#fromYear').val() > $('#toYear').val()) {
        $('#toYear').val('');  
    }
});
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
                            const listItem = document.getElementById(`li_${archiveID}`)
                            const searchResult = document.getElementById('search-result');
                            if (listItem){
                                listItem.remove();
                            }
                            if (!searchResult.querySelector('.project-list')) {
                                searchResult.innerHTML = "<p style='text-align: center'>No uploaded research found.</p>";
                            }
                        }
                    });
                }
            });
        }
    });
}

window.onpopstate = function(event) {
    filteredData();
}; 

$('#search-result').on('click', '.item-abstract', function(event) {
    const $target = $(event.target);

    if ($target.closest('h3.abstract-title').length) {
        event.preventDefault();
        const abstractGroup = $(this).find('.abstract-group');
        const img = $(this).find('h3.abstract-title img');
        
        abstractGroup.slideToggle(200);
        
        const isArrowDown = img.attr('src').includes('arrow-down');
        img.attr('src', isArrowDown ? '../images/arrow-up.svg' : '../images/arrow-down.svg');
    }
    
    else if ($target.closest('.abstract-group').length) {
        $(this).find('.abstract-group').slideToggle(200);
        const img = $(this).find('h3.abstract-title img');
        const isArrowDown = img.attr('src').includes('arrow-up');
        img.attr('src', isArrowDown ? '../images/arrow-down.svg' : '../images/arrow-up.svg');
    }
});

function clearFilter() {
    $('#inputDepartment_search').val('');
    $('#department_course').val('').html('<option value=""></option>'); 
    $('#fromYear').val('');
    $('#toYear').val('');
    $('#research_date').val('');
    $('#searchInput').val('');
    
    tagify.removeAllTags();
    
    filteredData();
    
    $('#data-result').hide();
}

const emailInput = document.getElementById('project_members');
const emailTagify = new Tagify(emailInput, {
    pattern: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    delimiters: ",",
    maxTags: 5,
    dropdown: {
        enabled: 0
    },
    validate: function(tag) {
        const email = tag.value;
        const emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return emailRegex.test(email);
    }
});

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
    document.getElementById('project_members').value = emailTagify.value.map(tag => tag.value).join(',');
}

const filterKeywordsInput = document.getElementById('filter-keywords');
const keywordTagify = new Tagify(filterKeywordsInput, {
    delimiters: ",",
    maxTags: 7,   
    dropdown: {
        enabled: 0 
    }
});

function getKeywords() {
    return keywordTagify.value.map(tag => tag.value).join(',');
}

function getURLParameter(name) {
    return new URLSearchParams(window.location.search).get(name);
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
                // Upload progress from 0-50%
                const percentComplete = (e.loaded / e.total) * 50;
                progressBar.style.width = percentComplete + '%';
                progressBar.setAttribute('aria-valuenow', percentComplete);
                progressText.textContent = `Uploading file... ${Math.round(percentComplete)}%`;
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                // Show file content checking progress 50-90%
                progressBar.style.width = '75%';
                progressBar.setAttribute('aria-valuenow', 75);
                progressText.textContent = 'Checking file content...';

                setTimeout(() => {
                    // Complete the progress bar
                    progressBar.style.width = '100%';
                    progressBar.setAttribute('aria-valuenow', 100);
                    progressText.textContent = 'Uploading complete!';

                    // Short delay before showing result
                    setTimeout(() => {
                        const result = JSON.parse(xhr.responseText);
                        loadingOverlay.style.display = 'none';
                        if (result.status === 'success') {
                            updateResearchList(result);
                            swal({
                                title: result.stats,
                                text: result.message,
                                type: result.status,
                                confirmButtonText: 'Okay',
                            }, function(isConfirm) {
                                if(isConfirm) {
                                    $('#modelId').modal('hide');
                                    $('.modal-backdrop').remove();
                                    form.reset();
                                    submitButton.disabled = false;
                                    submitButton.textContent = 'Upload';
                                    progressBar.style.width = '0%';
                                    
                                    // Update UI with new research entry
                                }
                            });
                        } else {
                            swal({
                                title: 'Error',
                                text: result.message || 'Upload failed',
                                type: 'error',
                                confirmButtonText: 'Okay'
                            });
                            submitButton.disabled = false;
                            submitButton.textContent = 'Upload';
                            progressBar.style.width = '0%';
                        }
                    }, 500); // Delay before showing swal
                }, 1000); // Delay for final progress
            }
        };
        xhr.onerror = function() {
            loadingOverlay.style.display = 'none';
            swal({
                title: 'Error',
                text: 'An error occurred during upload',
                type: 'error',
                confirmButtonText: 'Okay'
            });
            submitButton.disabled = false;
            submitButton.textContent = 'Upload';
            progressBar.style.width = '0%';
        };

        xhr.send(formData);
        
    } catch (error) {
        loadingOverlay.style.display = 'none';
        swal({
            title: 'Error',
            text: error.message || 'An error occurred during upload',
            type: 'error',
            confirmButtonText: 'Okay'
        });
        submitButton.disabled = false;
        submitButton.textContent = 'Upload';
        progressBar.style.width = '0%';
    }
});

// Function to update the research list
function updateResearchList(result) {
    const noResearchMessage = document.querySelector("#search-result p");
    if (noResearchMessage) {
        noResearchMessage.remove();
    }
    
    const newLi = document.createElement('li');
    newLi.className = 'project-list item';
    newLi.id = `li_${result.archiveID}`;
    
    newLi.innerHTML = `
        <div class="item-body">
            <div class="project-tag">
                <span class="badge badge-${result.document_status === 'Accepted' ? 'success' : 'danger'} tag" style="font-size: 12px;">
                    ${result.document_status === 'Accepted' ? 'Published' : result.document_status}
                </span>
            </div>
            <div class="item-title">
                <h4><a href="view_project_research.php?archiveID=${result.archive_id}">${result.project_title}</a></h4>
            </div>
            <div class="item-content">
                <p>${result.project_members}</p>
            </div>
            <div class="item-meta">
                <p>${result.department}</p>
                <p>Archive ID: ${result.archive_id}</p>
                <p>${result.date_published ? `Published: ${result.date_published}` : 'Not yet published'}</p>
            </div>
            <div class="item-abstract">
                <h3 class="abstract-title">
                    <a href="#"><span>Abstract</span>
                    <img src="../images/arrow-down.svg" style="width: .675rem; height: .675rem" alt=""></a>
                </h3>
                <div class="abstract-group" style="display:none">
                    <section class="item-meta">
                        <div class="abstract-article">
                            <p>${result.project_abstract}</p>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div class="project-action">
            <button  onclick="confirmDelete(${result.archiveID})" class="btn"><img style="width: 20px; height: 20px" src="images/svg/delete.svg" title="Delete Research"></img></a>            
        </div>
    `;
    
    const searchResult = document.getElementById('search-result');
    searchResult.insertBefore(newLi, searchResult.firstChild);
}
    function validateForm() {
        const projectTitle = document.querySelector('input[name="project_title"]');
        const abstract = document.querySelector('textarea[name="abstract"]');
        const fileInput = document.querySelector('input[name="project_file"]');
        
        if (!projectTitle.value || !abstract.value || !fileInput.files[0]) {
            alert('Please fill all required fields');
            return false;
        }
        
        const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!validTypes.includes(fileInput.files[0].type)) {
            alert('Please upload a PDF, DOC, or DOCX file');
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
    document.getElementById('pdfFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!validTypes.includes(file.type)) {
                swal({
                    title: 'Invalid File Type',
                    text: 'Only PDF files are allowed.',
                    type: 'error',
                    confirmButtonText: 'Okay'
                });
                e.target.value = '';
                return;
            }

            // Check file size (20 MB limit)
            if (!validateFileSize(file, 20)) {
                e.target.value = '';
            }
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        // Get parameters from the URL
        const searchInput = getURLParameter('searchInput');
        const researchDate = getURLParameter('research_date');
        const documentStatus = getURLParameter('documentStatus');
        const fromYear = getURLParameter('fromYear');
        const toYear = getURLParameter('toYear');
        const keywords = getURLParameter('keywords');

        // Set the values of inputs or selects based on the URL parameters
        if (searchInput) document.getElementById('searchInput').value = searchInput;
        if (researchDate) document.getElementById('research_date').value = researchDate;
        if (documentStatus) document.getElementById('documentStatus').value = documentStatus;
        if (fromYear) document.getElementById('fromYear').value = fromYear;
        if (toYear) document.getElementById('toYear').value = toYear;
        if (keywords) document.getElementById('keywords').value = keywords;
    });
    
    function filteredData(){
        var owner_email = '<?= $_SESSION['auth_user']['student_email']?>';
        var documentStatus = $('#documentStatus').val();
        var fromYear = $('#fromYear').val();
        var toYear = $('#toYear').val();
        var keywords = getKeywords(); 
        var searchInput = $('#searchInput').val();
        var research_date = $('#research_date').val();
        var page = 1 ;
        var limit = <?= isset($limit) ? $limit : 10 ?>;

        if (documentStatus === 'Rejected'){
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
                keywords: keywords,
                fromYear: fromYear,
                toYear: toYear,
                research_date: research_date,
                page: page,
                limit: limit
            },
            success: function(response){
                $('#search-result').html(response.html);
                $('#resultNumber').text(response.totalFilteredCount); 
                if(response.count > 0) {
                    $('#search-result').html(response.html);
                } else {
                    $('#search-result').html("<p class='text-center' style='color: #666; font-size: 14px; font-weight:400'>No research paper found.</p>");
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
            error: function(xhr, status, error){
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    }

    keywordTagify.on('add', filteredData);
    keywordTagify.on('remove', filteredData);

    $('#documentStatus, #research_date, #fromYear, #toYear').change(filteredData);
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