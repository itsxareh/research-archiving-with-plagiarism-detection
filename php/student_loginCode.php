<?php 
	

include '../connection/config.php';
$db = new Database();

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
		
		if(isset($_POST['submit']))
		{
			$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '';
			$email = trim($_POST['email']);
			$password = $_POST['password'];
			$searchResults = $db->studentLogin($email, $password, $redirect_to);

		}

?>

