<?php

include '../connection/config.php';
session_start();

$db = new Database();
$admin_id = $_SESSION['auth_user']['admin_id'];

//display all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../phpmailer/src/PHPMailer.php';
include '../phpmailer/src/SMTP.php';


date_default_timezone_set('Asia/Manila');
$date = date("Y-m-d");
$time = date("H:i:s");

if(isset($_GET['archiveID'])){

    $archiveID = $_GET['archiveID'];

    $select_research_owner_email = $db->SELECT_RESEARCH_OWNER_EMAIL($archiveID);

    if ($select_research_owner_email){
        $sql1 = $db->publish_research($archiveID);
        $sql2 = $db->adminsystem_INSERT_NOTIFICATION($admin_id, "You have published a research paper.", $date, $time);
        $sql3 = $db->student_INSERT_NOTIFICATION($select_research_owner_email['studentID'], "Your " . $select_research_owner_email['project_title'] . " research paper has been published.", $date, $time);
    
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'researcharchiverplagiarism@gmail.com';
        $mail->Password = 'wqjd ukqy plvb liyq';
        $mail->SMTPSecure = 'ssl';  
        $mail->Port = 465;
    
        $mail->setFrom('researcharchiverplagiarism@gmail.com');
        $mail->addAddress($select_research_owner_email['email']);
        $mail->isHTML(true);
        $mail->Subject = 'EARIST Research Repository Research Paper Status';
        $mail->Body = 'Your ' . $select_research_owner_email['project_title'] . ' research paper has been published.';
    
        if($mail->send()){
            echo json_encode(array("status_code" => "success", "status" => "Research published", "alert" => "Success"));
        } else {
            echo json_encode(array("status_code" => "error", "status" => "Failed to send email", "alert" => "Oppss..."));
        }
    } else {
        $sql1 = $db->publish_research($archiveID);
        $sql2 = $db->adminsystem_INSERT_NOTIFICATION($admin_id, "You have published a research paper.", $date, $time);

        echo json_encode(array("status_code" => "success", "status" => "Research published", "alert" => "Success"));
    }

}