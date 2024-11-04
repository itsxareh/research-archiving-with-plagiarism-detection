<?php

include '../connection/config.php';
$db = new Database();
//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
if($_SESSION['auth_user']['admin_id']==0){
  header("Location: login.php");
  exit();
}

function highlightPlagiarizedWords($submitted_sentence, $existing_sentence) {
  $submitted_words = explode(' ', $submitted_sentence);
  $existing_words = explode(' ', $existing_sentence);

  $common_words = array_intersect(array_map('strtolower', $submitted_words), array_map('strtolower', $existing_words));

  $highlighted_submitted = '';
  $highlighted_existing = '';

  foreach ($submitted_words as $word) {
      if (in_array(strtolower($word), $common_words)) {
          $highlighted_submitted .= '<span class="highlight">' . htmlspecialchars($word) . '</span> ';
      } else {
          $highlighted_submitted .= htmlspecialchars($word) . ' ';
      }
  }

  foreach ($existing_words as $word) {
      if (in_array(strtolower($word), $common_words)) {
          $highlighted_existing .= '<span class="highlight">' . htmlspecialchars($word) . '</span> ';
      } else {
          $highlighted_existing .= htmlspecialchars($word) . ' ';
      }
  }

  return [
      'submitted' => trim($highlighted_submitted),
      'existing' => trim($highlighted_existing)
  ];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Archive Research: EARIST Research Archiving System</title>

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

    <!-- Common -->
    <link href="css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="css/lib/themify-icons.css" rel="stylesheet">
    <link href="css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="css/lib/helper.css" rel="stylesheet">
    <link href="../student/css/style.css" rel="stylesheet">
    <link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
</head>

<body>
<!---------NAVIGATION BAR-------->
<?php
require_once 'templates/admin_navbar.php';
?>
<!---------NAVIGATION BAR ENDS-------->



<div class="content-wrap">
  <div class="container">
      <div class=" col-md-12">

<!-- Page Heading -->
        <div class="row">
          <div class="col-md-12 p-r-0 title-margin-right">
            <div class="page-header">
              <div class="page-title">
                <h1>Research Article
                </h1>
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
      
  } else { 
    header('Location: bad-request.php');
  }
  ?>
    <div class="col-md-8">
        <div class="short-info">
            <p><strong style="font-size: 20px; color:#313131"><?php echo ucwords($data['project_title']); ?> </strong><br></p>
            <p class="detail-font"><?php echo $data['project_members']; ?></p>
            <?php if (!empty($data['date_published'])) {
              $first_published = DateTime::createFromFormat("Y-m-d", $data['date_published'])->format("d F Y");
              echo '<p class="detail-font">Published: '.$first_published.' | '. $data['archive_id'] .'</p>'; }
              else {
                echo '<p class="detail-font">Not yet published</p>';
              }
            ?>
        </div>
      <div class="form-group" style="padding-top: 1rem;">
        <iframe src="<?php echo $data['documents']; ?>" width="100%" height="900px" allowfullscreen></iframe>
    </div>
    </div>
    <div class="col-md-4">
    <div class="title-margin-left">
      <div class="page-header">
        <div class="page-title information-meta">
        <span class="info-font text-white"></i>RESULTS</span>
        </div>
      </div>
    </div>
    <?php
    if ($data['aid'] !== ''){
      $archive_id = $data['aid'];
      $data = $db->SELECT_PLAGIARISM_SUMMARY_RESEARCH($archive_id);
      if (!empty($data)) {
        $percentage = $data['plagiarism_percentage'];
        if ($percentage >= 100){
          $percentage = 100;
        }
        echo '<div class="info-container" style="display: flex; justify-content:space-between; align-items: center">
                <p class="info-meta" style="font-size: 14px; margin-bottom: 0; font-weight: 500">Plagiarized Content</p>
                <div class="d-flex align-items-center">
                  <div class="progress w-100" style="height: 10px">
                    <div class="progress-bar-danger progress-bar" role="progressbar" style="width: '.round($percentage, 1).'%" aria-valuenow="'.round($percentage, 1).'" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                  <span style="color: #a33333; font-size: 16px; margin-left: .75rem !important;">'.round($percentage, 1).'%</span>
                </div>
              </div>';
      }
    } ?> 
    
    <ul class="plagiarized-container">
    <?php
    $result = $db->SELECT_PLAGIARISM_RESULTS_RESEARCH($archive_id);
    if (!empty($result)) {
        foreach ($result as $results) {
            $highlighted_result = highlightPlagiarizedWords($results['submitted_sentence'], $results['existing_sentence']);

            echo '
            <li class="plagiarized-card">
              <div class="plagiarized-card-content">
                <p class="info-meta" style="color: #000; padding-bottom: 0; font-size: 18px; margin-bottom: 0; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis">'.ucwords($results['project_title']).'</p>
                <a class="info-meta" style="font-size: 12px; padding-top: 0;" href="read_full.php?archiveID='.$results['archive_id'].'"><i class="pread">Read full here</i></a>
                <p class="info-meta" style="color: #373757; padding-top: 6px; padding-bottom: 0; font-size: 16px; margin-bottom: 0; font-weight: 500">'.DateTime::createFromFormat("Y-m-d", $results['dateOFSubmit'])->format("F d Y").'</p>
                
                <p class="info-meta" style="font-size: 12px; margin-bottom: 0; padding-bottom: 0; padding-top: 6px; font-weight: 500">Submitted sentence</p>
                <p class="info-meta" style="color: #373757; font-size: 16px; margin-bottom: 0; padding-top: 0; font-weight: 400">'.$highlighted_result['submitted'].'</p>
                
                <p class="info-meta" style="font-size: 12px; margin-bottom: 0; padding-bottom: 0; padding-top: 6px; font-weight: 500">Plagiarized sentence</p>
                <p class="info-meta" style="color: #373757; font-size: 16px; margin-bottom: 0; padding-top: 0; font-weight: 400">'.$highlighted_result['existing'].'</p>
                
                <p class="info-meta" style="font-size: 16px; margin-bottom: 0; padding-top: 6px; font-weight: 500; color: #a33333">'.round($results['similarity_percentage'], 1).'% similar words</p>
              </div>
            </li>';
        }
    }
    ?>
    </ul>
      </div>
    </div>
  </div>
</div>



</div>



</div>                                        

</div>
        </section>
      </div>
    </div>
  </div>


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