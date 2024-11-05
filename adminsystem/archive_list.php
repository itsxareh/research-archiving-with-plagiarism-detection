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
    <title>Archived List: EARIST Research Archiving System</title>
    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="images/logo2.png">
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
    <link href="../css/action-dropdown.css" rel="stylesheet">
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
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



    <div class="content-wrap">
            <div class="main container-fluid">
                <div class="col-md-12 p-r-0 title-page">
                    <div class="page-header">
                        <div class="page-title">
                            <h1>Researches</h1>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Research</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                            </div>

                            <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group">

                                  <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Research Title</label>
                                    <input type="text" class="form-control" name="project_title" placeholder="Enter Research Title...">
                                    </div>

                                    <div class="col-sm-6">
                                    <label for="">Enter a year</label>
                                    <input type="number" class="form-control" name="year" min="1900" max="2100" placeholder="Ex: 2024">
                                    </div>
                                  </div>
                                  
                                  <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Select Department</label>
                                  <select id="inputDepartment" name="department" class="selectpicker form-control" required title="Select Department">
                                    <option></option>
                                    <?php 
                                        $res = $db->showDepartments_WHERE_ACTIVE();

                                        foreach ($res as $item) {
                                        echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                                        }
                                    ?>
                                    
                                  </select>
                                    </div>

                                    <div class="col-sm-6">
                                    <label for="">Select Course</label>
                                    <div class="course_dropdown" id="course"></div>
                                    </div>
                                  </div>
                                  
                                  <label for="">Abstract</label>
                                  <textarea class="form-control" name="abstract" placeholder="The Research Abstract..."></textarea>
                                  
                                  <label for="">Project Members</label>
                                  <input type="text" class="form-control" name="project_members" placeholder="Ex: John Doe, Peter Parker, Tony Stark">
                                  

                                  <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="">Project Owner Email</label>
                                    <input type="email" class="form-control" name="owner_email" placeholder="johndoe@example.com">
                                    </div>

                                    <div class="col-sm-6">
                                    <label for="">Project File (PDF)</label>
                                    <input type="file" accept=".pdf" class="form-control" name="project_file" >
                                    </div>
                                  </div>
                                  
                                  
                                  

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button name="add_research" class="btn btn-primary">Save</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
                <section class="project-page-content">
                <div class="col-md-12">
                    <div class="add-research">
                        <button type="button" class="add-research-button item-meta" data-toggle="modal" data-target="#modelId">
                        <i class="ti-plus m-r-4"></i> Add New Research
                        </button>
                    </div>
                    <!-- <div class="advance-filter-search">
                        <p class="font-black bold">Filter</p>
                        <div class="mb-3 mb-sm-0">
                            <label for="" class="item-meta">Select Department</label>
                            <select id="inputDepartment_search" name="department" class="selectpicker form-control item-meta" required>
                            <option value=""></option>
                            <?php 
                                $res = $db->showDepartments_WHERE_ACTIVE();

                                foreach ($res as $item) {
                                echo '<option value="'.$item['id'].'">'.$item['name'].'</option>';
                                }
                            ?>
                            
                            </select>
                        </div>
                        <div class="mb-3 mb-sm-0">
                            <label class="item-meta" for="course">Course:</label>
                            <select id="department_course" name="department_course" class="selectpicker form-control" required> 
                            <option value=""></option>
                            </select>
                        </div>
                        <fieldset>
                            <div class="input-filter-group">
                                <label class="item-meta" for="info-label">Document Status:</label>
                                <select class="item-meta" style="width: auto;" name="documentStatus" id="documentStatus">
                                    <option value="">All</option>
                                    <option value="Accepted">Published</option>
                                    <option value="Not Accepted">Not yet published</option>
                                </select>
                        </fieldset>
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
                </div> -->
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
                                    <th class="list-th">Archive Code</th>
                                    <th class="list-th">Research Title</th>
                                    <th class="list-th">College</th>
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
                                    <td class="list-td"><?= DateTime::createFromFormat("Y-m-d", $result['dateOFSubmit'])->format("F d Y"); ?></td>
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
                                            <?= $status == 'Accepted' ? 'Published' : 'Not Published'?>
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
                                                <a href="view_archive_research.php?archiveID=<?= $result['aid'] ?>" class="dropdown-action-item">View</a>
                                                <?php 
                                                 $document_status = $result['document_status'];
                                                 if ($document_status === 'Accepted'){
                                                    echo '<a href="unpublish_research.php?archiveID='.$result['archiveID'].'" class="dropdown-action-item">Unpublish</a>';
                                                 } elseif ($document_status === 'Not Accepted'){
                                                    echo '<a href="publish_research.php?archiveID='.$result['archiveID'].'" class="dropdown-action-item">Publish</a>';
                                                 }
                                                ?>
                                                <a href="delete_research.php?archiveID=<?= $result['archiveID'] ?>" class="dropdown-action-item">Delete</a>
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
            </section>
        </div>
    </div>


<script>
    new DataTable('#datatablesss');
    $('#datatablesss_filter label input').removeClass('form-control form-control-sm');
    $('#datatablesss_wrapper').children('.row').eq(1).find('.col-sm-12').css({
    'padding-left': 0,
    'padding-right': 0
    });
    // $('#datatablesss_wrapper').children('.row').eq(1).children('.col-sm-12').children('.dataTables_wrapper').find('.dataTables_length, .dataTables_filter').css({
    // 'display': 'none'
    // });
    $('#inputDepartment_search').change(function() {
        var department = $(this).val();
        table.columns(3).search(department).draw(); // 3 is the column index of "Department"
    });
</script>

<!-- Add this script after including the DataTables and jQuery libraries -->
<script>
//     const department = document.getElementById('#inputDepartment');
//     var dept = department.val();
//     console.log(dept);
// $("#inputDepartment").change(function(){
    
//     if(department != " "){
//             $.ajax({
//                 url:"show_course.php",
//                 method:"POST",
//                 data:{"send_department_set":1, "send_department":department},

//                 success:function(data){
//                 $("#department_course").html(data);
//                 $("#department_course").css("display","block");
//                 }
//             });
//             }else{
//             $("#department_course").css("display","none");
//             }

//     });
</script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
<script>
document.addEventListener("click", function(event) {
    // Check if the clicked element has the class 'action-button'
    if (event.target.classList.contains("action-button")) {
        // Get the student ID from the button's ID attribute
        const studentId = event.target.id.split("_")[1];
        console.log(studentId); // For debugging: log the student ID

        // Get the corresponding dropdown menu based on the button's ID
        const dropdown = document.getElementById(`dropdown_${studentId}`);
        
        // Hide all other dropdowns first
        document.querySelectorAll(".dropdown-action").forEach((dropdown) => {
            dropdown.style.display = "none";
        });

        // Toggle the display of the clicked button's dropdown
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }
    }
});
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