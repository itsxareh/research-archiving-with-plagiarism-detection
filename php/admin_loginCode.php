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
		
		if(isset($_POST['email']) && isset($_POST['password']))
		{	
			$email = trim($_POST['email']);
			$password = $_POST['password'];
			$searchResults = $db->adminLogin($email, $password);
			$verification_code = rand(100000, 999999);

			$searchResults = json_decode($searchResults, true);

			if($searchResults['alert'] == 'Account Verification'){
				$sql = $db->admin_recover_code_with_email($email, $verification_code);
				
				$_SESSION['email'] = $email;

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
				$mail->Subject = 'Account Verification Code';
				$mail->Body = 'Your account OTP code is <strong> ' . $verification_code . '.</strong> Please use this code to verify your account.';
				$mail->send();
			
				echo json_encode($searchResults);
			} else {
				echo json_encode($searchResults);
			}
		}

?>

