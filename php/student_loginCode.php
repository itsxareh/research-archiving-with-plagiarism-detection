<?php 
	

include '../connection/config.php';
$db = new Database();

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
		
		if(isset($_POST['LogIn']))
		{
			$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
			$email = $_POST['studentEmail'];
			$password = md5($_POST['studentPword']);
			$searchResults = $db->studentLogin($email, $password, $redirect_to);
			        
				
		}

?>

