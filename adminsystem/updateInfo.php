<?php
include '../connection/config.php';
session_start();

$db = new Database();

if (isset($_SESSION['auth_user']['admin_id'])) {
    $admin_id = $_SESSION['auth_user']['admin_id'];
    $fname = $_POST['first_name'];
    $mname = $_POST['middle_name'];
    $lname = $_POST['last_name'];
    $pnumber = $_POST['pnumber'];

    $currentData = $db->admin_profile($admin_id);

    if ($currentData) {

        $stmt = $db->UPDATE_admin_info_onSETTINGS($fname, $mname, $lname, $pnumber, $admin_id);
        
        $response = array(
            'status' => 'success',
            'stats' => 'Success',
            'message' => 'Updated successfully',
            'first_name' => $fname,
            'middle_name' => $mname,
            'last_name' => $lname,
            'pnumber' => $pnumber
        );
    } else {
        $response = array(
            'status' => 'info',
            'stats' => 'No changes',
            'message' => 'No updates made. The information is already up to date.',
        );
    }
    echo json_encode($response);
} else {
    echo json_encode(array('status' => 'error','message' => 'Incomplete data.'));
}

?>