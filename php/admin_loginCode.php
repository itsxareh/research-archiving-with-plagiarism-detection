<?php 
	

include '../connection/config.php';
$db = new Database();

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
		
		if(isset($_POST['LogIn']))
		{
            
            
			$email = $_POST['adminEmail'];
			$password = md5($_POST['adminPword']);
			
			$searchResults = $db->adminLogin($email, $password);
			        
				
		}

?>
