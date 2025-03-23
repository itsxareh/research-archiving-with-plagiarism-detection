<?php

include '../connection/config.php';
$db = new Database();
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
$userRole = $db->getRoleById($_SESSION['auth_user']['role_id']);
$permissions = explode(',', $userRole['permissions']);

// Helper function to check permissions
function hasPermit($permissions, $permissionToCheck) {
    foreach ($permissions as $permission) {
        if (strpos($permission, $permissionToCheck) === 0) {
            return true;
        }
    }
    return false;
}
if($_SESSION['auth_user']['admin_id']==0){
    echo"<script>window.location.href='index.php'</script>";
    exit(); 
    
} elseif(!hasPermit($permissions, 'research_view')) {
    header('Location:../../bad-request.php');
    exit(); 
} else {
    $admin_id = $_SESSION['auth_user']['admin_id'];
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Archive Research: EARIST Repository</title>

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

    <!-- Common -->
    <link href="../css/styles.css" rel="stylesheet">
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="../student/css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
      @media (max-width: 767px){
        .content-wrap {
          height: auto;
        }
      }

    </style>
</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->
<div class="content-wrap">
  <div class="container">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-12">
            <div class="page-header">
              <div class="page-title">
                <h1>Research Article</h1>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-body inf-content">
          <div class="row">
            <?php
            if(isset($_GET['archiveID'])){

              $archiveID = $_GET['archiveID'];

              $data = $db->SELECT_ARCHIVE_RESEARCH($archiveID);
                
            }
            ?>
          <div class="col-md-8">
            <div class="short-info">
              <p><strong style="font-size: 20px; color:#313131"><?php echo ucwords($data['project_title']); ?> </strong><br></p>
              <p class="detail-font"><?php echo $data['project_members']; ?></p>
              <?php if (!empty($data['date_published'])) {
                  $first_published = (new DateTime($data['dateOFSubmit']))->format("d F Y");
                  echo '<p class="detail-font">Published: '.$first_published.' | Archive ID: '. $data['archive_id'] .'<span class="float-right"><i class="ti-eye m-r-4 "></i>'.$data['view_count'].'</span></p>'; }
                  else {
                    echo '<p class="detail-font">Not yet published | Archive ID: '. $data['archive_id'] .'</p>';
                  }
                ?>
            </div>
            <div class="form-group" style="padding-top: 1rem;">
              <div class="abstract-group">
               <p style="font-size: 22px; color: #313131; margin-bottom: .275rem">Abstract</p>
               <p style="height: auto; background:none; border: none; margin: 0" class="detail-font" id="projectAbstract" readonly><?php echo $data['project_abstract']; ?></p>
              </div>
              <br> 
              <?php if (isset($_SESSION['auth_user'])):
                  $user_id = $_SESSION['auth_user']['admin_uniqueID'];
                  $archive_id = $data['archive_id'];
                  $is_super_admin = isset($_SESSION['auth_user']['admin_id']) && $_SESSION['auth_user']['role_id'] == 1;
                  
                  if ($is_super_admin): // If admin with role_id 0, grant automatic access
              ?>
                  <a style="color: #BB0505;" href="read_full.php?archiveID=<?php echo $data['archive_id']; ?>">Read full text</a>
              <?php else: // For students and other admin roles, check access status
                  $access_status = $db->GET_ACCESS_STATUS($archive_id, $user_id);
                  if ($access_status == 'approved'): 
              ?>
                  <a style="color: #BB0505;" href="read_full.php?archiveID=<?php echo $data['archive_id']; ?>">Read full text</a>
              <?php elseif ($access_status == 'pending'): ?>
                  <div class="alert alert-info mt-2">
                      <p class="m-0">Your access request for this document is pending approval.</p>
                  </div>
              <?php elseif ($access_status == 'denied'): ?>
                  <div class="alert alert-danger mt-2">
                      <p class="m-0">Your access request for this document was denied.</p>
                      <a href="javascript:void(0);" class="request-access-btn">Request Access Again</a>
                  </div>
              <?php else: ?>
                  <a href="javascript:void(0);" class="request-access-btn" style="color: #BB0505;"><?= $access_status ?>Request access to read full text</a>
              <?php endif; ?>
              <?php endif; ?>
              <?php else: // Not logged in ?>
                  <a style="color: #BB0505;" href="javascript:void(0);" data-require-login="true">Read full text</a>
              <?php endif; ?>
            </div>
          </div>
          <div class="col-md-4">
          <div class="">
                  <div class="page-header">
                    <div class="page-title information-meta">
                      <span class="info-font text-white"><i class="ti-info text-black" style="background-color: white; border-radius: 50%; margin-right:6px"></i>Information</span>
                    </div>
                  </div>
                </div>
            <div class="info-container">
              <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Details</p>
              <ul>
                <li class="info-meta"><label>Course:</label><?= $data['course_name'] ?></li>
                <li class="info-meta"><label>Department:</label><?= $data['name'] ?></li>
                <li class="info-meta"><label>Contact Email:</label><a href="view_profile.php?studID=<?= $data['sid'] ?>"><?= $data['research_owner_email'] ?><i class="ti-arrow-top-right"></i></a></li>
              </ul>
            </div>
            <div class="info-container">
              <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Publication History</p>
              <ul>
                <li class="info-meta"><label>Project Year:</label><?= $data['project_year'] ?></li>
                <li class="info-meta"><label>Date Uploaded:</label><?= (new DateTime($data['dateOFSubmit']))->format("d F Y"); ?></li>
                <li class="info-meta"><label>Date Published:</label>
                  <?php if (!empty($data['date_published'])) {
                    $first_published = DateTime::createFromFormat("Y-m-d", $data['date_published'])->format("d F Y");
                    echo $first_published; } 
                    else {
                      echo "Not yet";
                    }
                  ?>
                </li>
              </ul>
            </div>
            <?php
                  if ($data['keywords'] !== ''){
                    echo '<div class="info-container">
                            <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Keywords</p>
                            <ul class="ul-keywords">';
                            $keywords = explode(',', $data['keywords']);
                            foreach ($keywords as $keyword) {
                              echo '<li class="info-meta"><span class="info-keywords">'.$keyword.'</span></li>'; 
                            }
                            echo '</ul>
                          </div>';
                  }
                ?>
                <?php
    if ($data['aid'] !== ''){
      $archive_id = $data['aid'];
      $smtm = $db->SELECT_PLAGIARISM_SUMMARY_RESEARCH($archive_id);
      if (!empty($smtm)) {
        $percentage = $smtm['total_percentage'];
        if ($percentage >= 100){
          $percentage = 100;
        }
        echo '<div class="info-container">
                <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Results</p>
                <ul>
                  <li class="info-meta">
                    <label>Plagiarism Match:</label>
                    <div class="d-flex align-items-center">
                      <div class="progress w-100" style="height: 10px">
                        <div class="progress-bar-danger progress-bar" role="progressbar" style="width: '.round($percentage, 1).'%" aria-valuenow="'.round($percentage, 1).'" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span style="color: #a33333; font-size: 16px; margin-left: .75rem !important;">'.round($percentage, 1).'%</span>
                    </div>
                  </li>
                  <li class="info-meta"><label class="see-more"><a href="plagiarism_result.php?archiveID='.$data['archiveID'].'">See more</a></label></li>
                </ul>
              </div>';
      } else { 
        echo '<div class="info-container">
                <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Results</p>
                <ul>
                  <li class="info-meta">
                    <label>Plagiarism Match:</label>
                    <div class="d-flex align-items-center">
                      <div class="progress w-100" style="height: 10px">
                        <div class="progress-bar-danger progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span style="color: #a33333; font-size: 20px; margin-left: .75rem !important;">0%</span>
                    </div>
                  </li>
                </ul>
              </div>';
      }
    } ?>
          </div>
        </div>
      </div>
    </div>
  </div>                                        
</div>
<div id="request-access-popup" tabindex="-1"
    class="bg-black/50 hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 h-full items-center justify-center flex">
    <div class="relative p-4 w-full max-w-md h-full h-auto">
        <div class="relative bg-white rounded-lg shadow">
            <button type="button"
                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center popup-close">
                <svg aria-hidden="true" class="w-5 h-5" style="width: 1.25rem !important;" fill="#000" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                      d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                      cliprule="evenodd">
                    </path>
                </svg>
                <span class="sr-only">Close popup</span>
            </button>

            <div class="p-15">
                <div class="text-center">
                    <p class="mb-3 text-2xl font-semibold leading-5 text-slate-900">
                        Request Access
                    </p>
                </div>
                
                <form id="access-request-form" method="post" action="">
                    <input type="hidden" name="archive_id" value="<?php echo $data['archiveID']; ?>">
                    
                    <div class="mt-4">
                        <label for="request_reason" class="block text-sm font-medium text-slate-700 mb-1">Reason for access request</label>
                        <textarea 
                            name="request_reason" 
                            id="request_reason" 
                            rows="4" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a33333]" 
                            required
                        ></textarea>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg hover:bg-[#c54b4b] bg-[#a33333] p-2 py-3 text-sm font-medium text-white outline-none focus:ring-2 focus:ring-black focus:ring-offset-1">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!----------------UPLOAD OR UPDATE AN IMAGE AND DISPLAYS THE SELECTED IMAGE FIRST BEFORE UPDATING OR UPLOADING--------------->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Request access button handlers
    document.querySelectorAll('.request-access-btn').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('request-access-popup').classList.remove('hidden');
        });
    });
    
    // Close popup handlers
    document.querySelectorAll('.popup-close').forEach(function(element) {
        element.addEventListener('click', function() {
            document.getElementById('request-access-popup').classList.add('hidden');
        });
    });
});

const requestForm = document.getElementById('access-request-form');
requestForm.addEventListener('submit', async function(e){
  e.preventDefault();

  const formData = new FormData(requestForm);
  $.ajax({
    url: '../php/process_request_access.php',
    type: 'POST',
    dataType: 'json',
    data: {
      archive_id: formData.get('archive_id'),
      request_reason: formData.get('request_reason')
    },
    success: function(response){
      document.getElementById('request-access-popup').classList.add('hidden');

      if(response){
        sweetAlert()
      }
    }
  })
})
</script>



    <!-- Common -->
    <script src="js/lib/jquery.min.js"></script>
    <script src="js/lib/jquery.nanoscroller.min.js"></script>
    <script src="js/lib/menubar/sidebar.js"></script>
    <script src="js/lib/preloader/pace.min.js"></script>
    <script src="js/lib/bootstrap.min.js"></script>
    <script src="js/scripts.js"></script>

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