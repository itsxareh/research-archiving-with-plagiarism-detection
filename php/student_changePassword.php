<?php 
include '../connection/config.php';
$db = new Database();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../phpmailer/src/PHPMailer.php';
include '../phpmailer/src/SMTP.php';

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
		
		if(isset($_POST['changePassword']) && isset($_SESSION['email'])){
			$email = isset($_POST['email']) ? $_POST['email'] : $_SESSION['email'] ;
			$npassword = trim($_POST['npassword']);
			$cnpassword = trim($_POST['cnpassword']);

			$user = $db->student_profile_by_email($_SESSION['email']);
			$student_id = $user['studID'];
			$student_email = $user['student_email'];
			$department_id = $user['department_id'];
			$course_id = $user['course_id'];

			if($npassword === $cnpassword){
				$searchResults = $db->student_change_password($email, $cnpassword);

				date_default_timezone_set('Asia/Manila');
				$date = date('F / d l / Y');
				$time = date('g:i A');
				$logs = 'You successfully recover your account.';
				$sql1 = $db->student_login_log($student_id, $email, $date, $time);
				
				session_unset($_SESSION['email']);

				$_SESSION['auth'] = true;
				$_SESSION['auth_user'] = [
					'student_id' => $student_id,
					'student_email' => $student_email,
					'department_id' => $department_id,
					'course_id' => $course_id,
				];

				$mail = new PHPMailer(true);

				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com';
				$mail->SMTPAuth = true;
				$mail->Username = 'researcharchiverplagiarism@gmail.com';
				$mail->Password = 'wqjd ukqy plvb liyq';
				$mail->SMTPSecure = 'ssl';
				$mail->Port = 465;

				$mail->setFrom('researcharchiverplagiarism@gmail.com');
				$mail->addAddress($email);
				$mail->isHTML(true);
				$mail->Subject = 'EARIST Research Archiver Account';
				$mail->Body = 'Recently your EARIST Research Archiver password has changed.';
				$mail->send();

				$_SESSION['alert'] = "Success";
				$_SESSION['status'] = "Log In Success";
				$_SESSION['status-code'] = "success";
				header("location: ../student/all_project_list.php");
				exit();

			} else {
				$_SESSION['alert'] = "Error!";
        		$_SESSION['status'] = "New password and current password doesn't match";
        		$_SESSION['status-code'] = "error";
				header("location: ../student/change_password.php");
				exit();
			}

		} else {
			$_SESSION['alert'] = "Error!";
            $_SESSION['status'] = "Invalid request";
            $_SESSION['status-code'] = "error";
            header("location:../student/login.php");
            exit();
		}

?>

