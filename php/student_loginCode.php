<?php 
	

include '../connection/config.php';
$db = new Database();

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
		
		if(isset($_POST['email']) && isset($_POST['password']))
		{
			$redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : '../student/all_project_list.php';
			$email = trim($_POST['email']);
			$password = $_POST['password'];
			$searchResults = $db->studentLogin($email, $password, $redirect_to);
			
			if($searchResults){
				echo json_encode(array('status_code' => 'success', 'status' => 'Login successful', 'alert' => 'Success', 'redirect' => $redirect_to));
			}else{
				echo json_encode(array('status_code' => 'error', 'status' => 'Invalid email or password', 'alert' => 'Oppss...'));
			}
		}

?>

