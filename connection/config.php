<?php 

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database {
  private $servername = "localhost";
  private $username = "root";
  private $password = "";
  private $database = "online_thesis";
  private $conn;

  public function __construct() {
      try {
          $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->database", $this->username, $this->password);
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      } catch(PDOException $e) {
          // Handle the exception here if needed
      }
  }

  public function getConnection() {
      return $this->conn;
  }

  //------------------------------------------------------------ADMIN PAGE CODE----------------------------------------------//


   //--------------------------------------------------------------------------------ADMIN REGISTER
  public function admin_register_select_email($email) {
    $connection = $this->getConnection();

    $stmt = $connection->prepare("SELECT * FROM admin_account WHERE admin_email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch(); # get user data

    return $result;
}

public function admin_register_select_phoneNumber($PhoneNumber) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM admin_account WHERE phone_number = ?");
  $stmt->execute([$PhoneNumber]);
  $result = $stmt->fetch(); # get user data

  return $result;
}

public function admin_register_INSERT_Info($uniqueId, $first_name, $middle_name, $last_name, $CompleteAddress, $PhoneNumber, $email, $pword, $imagePath, $verification_code) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO admin_account(uniqueID, first_name, middle_name, last_name, complete_address, phone_number, admin_email, admin_password, admin_profile_picture, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $sql->execute([$uniqueId, $first_name, $middle_name, $last_name, $CompleteAddress, $PhoneNumber, $email, $pword, $imagePath, $verification_code]);

}

public function admin_update_verify_status($verified, $adminID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_account SET verify_status=? WHERE id=?");
  $stmt->execute([$verified, $adminID]);

}


  //------------------------------------------------------------------------------------------------------ADMIN LOGIN
  public function adminLogin($email, $password) {
    $connection = $this->getConnection();

    $stmt = $connection->prepare("SELECT * FROM admin_account WHERE admin_email=? AND admin_password=? ");
    $stmt->execute([$email, $password]);

    while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $admin_id = $data['id'];
        $admin_UNIQUEid = $data['uniqueID'];
        $pword = $data['admin_password'];
        $verifystatus = $data['verify_status'];
    }

    if ($pword == $password) {
        if ($verifystatus == 'Not Verified') {
            // Handle account verification if needed
            $_SESSION['alert'] = "Account Verification";
            $_SESSION['status'] = "Verify your Account";
            $_SESSION['status-code'] = "info";
            header("location: ../adminsystem/admin_verify_account.php?id=$admin_id");
        } else {
            // Handle successful login
            date_default_timezone_set('Asia/Manila');
            $date = date('F / d l / Y');
            $time = date('g:i A');
            $logs = 'You successfully logged in to your account.';
            $online_offline_status = 'Online';

            $sql = $connection->prepare("INSERT INTO admin_systemnotification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
            $sql->execute([$admin_id, $logs, $date, $time]);

            $sql2 = $connection->prepare("UPDATE admin_account SET online_offlineStatus = ? WHERE id = ?");
            $sql2->execute([$online_offline_status, $admin_id]);

            $_SESSION['auth'] = true;
            $_SESSION['auth_user'] = [
                'admin_id' => $admin_id,
                'admin_uniqueID' => $admin_UNIQUEid
            ];

            $_SESSION['alert'] = "Success";
            $_SESSION['status'] = "Log In Success";
            $_SESSION['status-code'] = "success";
            header("location: ../adminsystem/dashboard.php");
        }
    } else {
        // Handle incorrect login details
        $_SESSION['alert'] = "Oppss...";
        $_SESSION['status'] = "Incorrect Log In Details";
        $_SESSION['status-code'] = "info";
        header("location: ../adminsystem/index.php");
    }
}

//------------------------------------------------------------------------------------------------------ADMIN LOGOUT_UPDATE TO OFFLINE
public function updateADMIN_OFFLINE($online_offline_status, $admin_id) {
  $connection = $this->getConnection();

  $sql2 = $connection->prepare("UPDATE admin_account SET online_offlineStatus = ? WHERE id = ?");
  $sql2->execute([$online_offline_status, $admin_id]);

}


//NOTIFICATION COUNT
public function adminsystemNOTIFICATION_COUNT($adminID, $unread) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) AS total_unread FROM admin_systemnotification WHERE admin_id = ? AND status = ?");
    $stmt->execute([$adminID, $unread]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUnread = $result['total_unread'];

    echo $totalUnread;

}


//ALL READ, UNREAD NOTIFICATIONS
public function adminsystemNOTIFICATION_Read_Unread($coordinatorID) {
  $connection = $this->getConnection();

      $stmt = $connection->prepare("SELECT * FROM admin_systemnotification LEFT JOIN admin_account ON admin_account.id = admin_systemnotification.admin_id WHERE admin_systemnotification.admin_id = ? ORDER BY admin_systemnotification.id DESC");
        $stmt->execute([$coordinatorID]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $notifications; // Add this line to return the notifications

}

//MARK ALL NOTIFICATIONS AS READ
public function adminsystemNOTIFICATION_MarkASRead($read, $admin_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_systemnotification SET status = ? WHERE admin_id = ?");
  $stmt->execute([$read, $admin_id]);

}

//INSERT ADMIN NOTIFICATIONS QUERIES
public function adminsystem_INSERT_NOTIFICATION($ID, $logs, $date, $time) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO admin_systemnotification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
  $sql->execute([$ID, $logs, $date, $time]);

}

public function adminsystem_INSERT_NOTIFICATION_2($admin_id, $logs, $date, $time) {
  $connection = $this->getConnection();

  $sql2 = $connection->prepare("INSERT INTO admin_systemnotification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
  $sql2->execute([$admin_id, $logs, $date, $time]);

}

public function adminsystem_INSERT_NOTIFICATION_3($adminID, $logs, $date, $time) {
  $connection = $this->getConnection();

  $sql2 = $connection->prepare("INSERT INTO admin_systemnotification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
  $sql2->execute([$adminID, $logs, $date, $time]);

}
//END INSERT ADMIN NOTIFICATIONS QUERIES





public function admin_profile($adminID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM admin_account WHERE id = ?");
	$stmt->execute([$adminID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}

public function SELECT_admin_profile($admin_id) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("SELECT admin_profile_picture FROM admin_account WHERE id = ? ");
  $sql->execute([$admin_id]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  $result = $row['admin_profile_picture'];

  return $result;

}

public function UPDATE_admin_profile($imagePath, $admin_id) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE admin_account SET admin_profile_picture = ? WHERE id = ?");
  $result = $sql->execute([$imagePath, $admin_id]);

  return $result;

}


public function UPDATE_admin_info_onSETTINGS($fname, $mname, $lname, $c_address, $cp_number, $adminID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_account SET first_name=?, middle_name=?, last_name=?, complete_address=?, phone_number=? WHERE id=?");
  $result = $stmt->execute([$fname, $mname, $lname, $c_address, $cp_number, $adminID]);

  return $result;

}


public function UPDATE_admin_password($npword, $adminID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_account SET admin_password = ? WHERE id=?");
  $result = $stmt->execute([$npword, $adminID]);

  return $result;

}


public function showAdmins($admin_id){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM admin_account WHERE id != ?");
  $stmt->execute([$admin_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function showDepartments(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM departments");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function showDepartments_WHERE_ACTIVE(){
  $connection = $this->getConnection();
  $status = 'Active';

  $stmt = $connection->prepare("SELECT * FROM departments WHERE department_status = ?");
  $stmt->execute([$status]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}


public function insert_Archive_Research($randomNumber, $student_id, $department, $department_course, $project_title, $date, $year, $abstract, $owner_email, $project_members, $pdfPath){
  $connection = $this->getConnection();
  
  $sql = $connection->prepare("INSERT INTO archive_research(archive_id, student_id, department_id, course_id, project_title, dateOFSubmit, project_year, project_abstract, research_owner_email, project_members, documents) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $sql->execute([$randomNumber, $student_id, $department, $department_course, $project_title, $date, $year, $abstract, $owner_email, $project_members, $pdfPath]);
  
}


public function showCourse($department){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM course WHERE department_id = ?");
  $stmt->execute([$department]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function showCourse_WHERE_ACTIVE($department){
  $connection = $this->getConnection();
  $status = 'Active';

  $stmt = $connection->prepare("SELECT * FROM course WHERE department_id = ? AND course_status = ?");
  $stmt->execute([$department, $status]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_ALL_StudentsData(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare(
    "SELECT 
      students_data.*, 
      students_data.id AS studID, 
      departments.name AS department_name, 
      course.course_name,
      COUNT(archive_research.id) AS total_research,
      SUM(CASE WHEN archive_research.document_status = 'Accepted' THEN 1 ELSE 0 END) AS published_research
    FROM 
        students_data
    LEFT JOIN 
        archive_research ON students_data.id = archive_research.student_id
    LEFT JOIN 
        departments ON departments.id = students_data.department_id
    LEFT JOIN 
        course ON course.id = students_data.course_id
    GROUP BY 
        students_data.id");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_StudentsData_WHERE_VERIFIED(){
  $connection = $this->getConnection();
  $status = 'Approved';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM students_data WHERE school_verify = ? ");
  $stmt->execute([$status]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_StudentsData_WHERE_NOT_VERIFIED(){
  $connection = $this->getConnection();
  $status = 'For Approval';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM students_data WHERE school_verify = ? ");
  $stmt->execute([$status]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_StudentsData($studID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, students_data.id AS studID FROM students_data 
  LEFT JOIN departments ON departments.id = students_data.department_id
  LEFT JOIN course ON course.id = students_data.course_id WHERE students_data.id = ?");
  $stmt->execute([$studID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}


public function SELECT_ALL_ARCHIVE_RESEARCH(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID, archive_research.archive_id as aid FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id 
  LEFT JOIN plagiarism_summary ON plagiarism_summary.archive_id = archive_research.id ORDER BY archive_research.date_published DESC");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_ALL_ARCHIVE_RESEARCH_WHERE_DEPARTMENT_FROM_DATE_PUBLISHED_TO_DATE_PUBLISHED($department, $from_date, $to_date){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id 
  WHERE archive_research.department_id = ? AND date_published BETWEEN ? AND ?");
$stmt->execute([$department, $from_date, $to_date]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH($limit, $offset) {
  $connection = $this->getConnection();
  $status = 'Accepted';

  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID,
    (SELECT COUNT(id) FROM archive_research_views WHERE archive_research_views.archive_research_id = archive_research.archive_id) AS view_count
    FROM archive_research 
    LEFT JOIN departments ON departments.id = archive_research.department_id
    LEFT JOIN course ON course.id = archive_research.course_id 
    WHERE archive_research.document_status = :status 
    ORDER BY view_count DESC 
    LIMIT :limit OFFSET :offset");

  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
  $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH_FILTER_DEPARTMENT_FROM_DATE_PUBLISH_TO_DATE_PUBLISH($department, $from_date, $to_date){
  $connection = $this->getConnection();
  $status = 'Accepted';

  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id WHERE archive_research.document_status = ? AND 
  archive_research.department_id = ? AND date_published BETWEEN ? AND ?");
  $stmt->execute([$status, $department, $from_date, $to_date]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH(){
  $connection = $this->getConnection();
  $status = 'Accepted';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE document_status = ? ");
  $stmt->execute([$status]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_UNPUBLISH(){
  $connection = $this->getConnection();
  $status = 'Not Accepted';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE document_status = ? ");
  $stmt->execute([$status]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE 1=1");
  $stmt->execute();
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_RECENT_RESEARCH_PAPER(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT archive_research.date_published, archive_research.project_title as project_title, archive_research.archive_id as aid FROM archive_research LEFT JOIN archive_research_views ON archive_research.archive_id = archive_research_views.archive_research_id WHERE archive_research.document_status = 'Accepted' GROUP BY archive_research_id ORDER BY archive_research.id DESC LIMIT 5;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_TOP_RESEARCH_CONTRIBUTOR(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, COUNT(*) as count, students_data.student_id as studID FROM archive_research LEFT JOIN students_data ON student_email = research_owner_email GROUP BY archive_research.research_owner_email ORDER BY count DESC LIMIT 5;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_PLAGIARIZED_RESEARCH_CONTENT(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, plagiarism_summary.archive_id as plagiarism_id, archive_research.archive_id as aid FROM plagiarism_summary LEFT JOIN archive_research ON archive_research.id = plagiarism_summary.archive_id ORDER BY plagiarism_summary.id DESC LIMIT 5;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_TOP_10_VIEWS_RESEARCH_PAPER(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count, archive_research.project_title as project_title, archive_research.archive_id as aid FROM archive_research LEFT JOIN archive_research_views ON archive_research.archive_id = archive_research_views.archive_research_id GROUP BY archive_research_id ORDER BY count DESC LIMIT 10;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_RESEARCH_PUBLISHED_PER_WEEK(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT date_published, COUNT(id) as count FROM `archive_research` WHERE document_status = 'Accepted' GROUP BY WEEK(date_published) ORDER BY date_published ASC LIMIT 5");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_RESEARCH(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research ");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_PUBLISHED_RESEARCH(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research WHERE document_status = 'Accepted'");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_UNPUBLISHED_RESEARCH(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research WHERE document_status = 'Not Accepted'");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_PLAGIARIZED_RESEARCH(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM plagiarism_summary");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_COURSES(){
  $connection = $this->getConnection();
  
  $stmt = $connection->prepare("SELECT COUNT(*) as total_count FROM course");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_ACTIVE_COURSES(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM course WHERE course_status = 'Active'");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_COUNT_ALL_DEPARTMENT(){
  $connection = $this->getConnection();
  
  $stmt = $connection->prepare("SELECT COUNT(*) as total_count FROM departments");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_ACTIVE_DEPARTMENT(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM departments WHERE department_status = 'Active'");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_ARCHIVE_RESEARCH($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT 
    archive_research.*,
    archive_research.archive_id AS archiveID,
    archive_research.id AS aid,
    students_data.student_id AS sid,
    departments.*,
    course.*,
    (SELECT COUNT(id) FROM archive_research_views WHERE archive_research_views.archive_research_id = archive_research.archive_id) AS view_count
    FROM archive_research
    LEFT JOIN departments ON departments.id = archive_research.department_id
    LEFT JOIN course ON course.id = archive_research.course_id
    LEFT JOIN students_data ON students_data.id = archive_research.student_id
    WHERE archive_research.archive_id = ?");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_PLAGIARISM_SUMMARY_RESEARCH($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM plagiarism_summary WHERE archive_id = ?");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_PLAGIARISM_RESULTS_RESEARCH($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, plagiarism_results.archive_id AS plaid FROM plagiarism_results LEFT JOIN archive_research ON plagiarism_results.similar_archive_id = archive_research.id  WHERE plagiarism_results.archive_id = ? ORDER BY archive_research.dateOFSubmit DESC");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH_FROM_STUDENT($student_email){
  $connection = $this->getConnection();
  $status = 'Accepted';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE document_status = ? AND research_owner_email = ?");
  $stmt->execute([$status, $student_email]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_NOT_PUBLISH_FROM_STUDENT($student_email){
  $connection = $this->getConnection();
  $status = 'Not Accepted';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE document_status = ? AND research_owner_email = ?");
  $stmt->execute([$status, $student_email]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_FROM_STUDENT($student_email){
  $connection = $this->getConnection();
  $status = 'Not Accepted';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE research_owner_email = ?");
  $stmt->execute([$student_email]);
  $result = $stmt->fetchColumn();

  return $result;
}


public function insert_Research_Views($archive_research_id, $student_id, $date){
  $connection = $this->getConnection();
  $sql = $connection->prepare("INSERT INTO archive_research_views(archive_research_id, student_id, date_of_views) VALUES (?, ?, ?)");
  $sql->execute([$archive_research_id, $student_id, $date]);
  
}



public function SELECT_ALL_DEPARTMENTS(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM departments ");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_DEPARTMENTS(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT COUNT(*) FROM departments ");
  $stmt->execute();
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ACTIVE_DEPARTMENTS(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT COUNT(*) FROM departments WHERE department_status = 'Active'");
  $stmt->execute();
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_ALL_DEPARTMENTS_WHERE_ACTIVE(){
  $connection = $this->getConnection();
  $status = 'Active';

  $stmt = $connection->prepare("SELECT * FROM departments WHERE department_status = ?");
  $stmt->execute([$status]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function insert_Department($department_code, $department_name, $description){
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO departments(dept_code, name, description) VALUES (?, ?, ?)");
  $sql->execute([$department_code, $department_name, $description]);
  
}

public function update_Department($department_code, $department_name, $description, $dept_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE departments SET dept_code = ?, name = ?, description = ? WHERE id = ?");
  $sql->execute([$department_code, $department_name, $description, $dept_id]);
  
}

public function unactivate_department($departmentID){
  $connection = $this->getConnection();

  $status = 'Not Activated';

  $sql = $connection->prepare("UPDATE departments SET department_status = ? WHERE id = ?");
  $sql->execute([$status, $departmentID]);

}

public function ACTIVATE_department($departmentID){
  $connection = $this->getConnection();

  $status = 'Active';

  $sql = $connection->prepare("UPDATE departments SET department_status = ? WHERE id = ?");
  $sql->execute([$status, $departmentID]);
  
}

public function delete_research($archive_id) {
  $connection = $this->getConnection();

  try {
      $sql = $connection->prepare("SELECT archive_id, similar_archive_id FROM plagiarism_results WHERE similar_archive_id = ?");
      $sql->execute([$archive_id]);
      $result = $sql->fetchAll(PDO::FETCH_ASSOC);

      $sql2 = $connection->prepare("SELECT archive_id, similar_archive_id FROM plagiarism_results WHERE archive_id = ?");
      $sql2->execute([$archive_id]);
      $result2 = $sql2->fetchAll(PDO::FETCH_ASSOC);

      if (count($result) > 0 || count($result2) > 0) {
          $sql = $connection->prepare("DELETE FROM plagiarism_results WHERE similar_archive_id = ?");
          $sql->execute([$archive_id]);
          $sql2 = $connection->prepare("DELETE FROM plagiarism_results WHERE archive_id = ?");
          $sql2->execute([$archive_id]);
          $sql3 = $connection->prepare("DELETE FROM plagiarism_summary WHERE similar_archive_id = ?");
          $sql3->execute([$archive_id]);
          $sql4 = $connection->prepare("DELETE FROM plagiarism_summary WHERE archive_id = ?");
          $sql4->execute([$archive_id]);

      }

      $sql = $connection->prepare("DELETE FROM archive_research WHERE id = ?");
      $sql->execute([$archive_id]);

  } catch (Exception $e) {
      throw $e;
  }
}

public function publish_research($archiveID){
  $connection = $this->getConnection();

  date_default_timezone_set('Asia/Manila');
  $date = date('Y-m-d');

  $status = 'Accepted';

  $sql = $connection->prepare("UPDATE archive_research SET document_status = ?, date_published = ? WHERE id = ?");
  $sql->execute([$status, $date, $archiveID]);
}

public function unpublish_research($archiveID){
  $connection = $this->getConnection();
  
  $date = '';

  $status = 'Not Accepted';

  $sql = $connection->prepare("UPDATE archive_research SET document_status = ?, date_published = ? WHERE id = ?");
  $sql->execute([$status, $date, $archiveID]);
}

public function update_document_status($archiveID, $status) {
  $query = "UPDATE archive_research SET document_status = :status WHERE archive_id = :archiveID";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':archiveID', $archiveID, PDO::PARAM_INT);

  return $stmt->execute();
}

public function update_department_status($departmentID, $status) {
  $query = "UPDATE departments SET department_status = :status WHERE id = :departmentID";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':departmentID', $departmentID, PDO::PARAM_INT);

  return $stmt->execute();
}

public function update_course_status($courseID, $status) {
  $query = "UPDATE course SET course_status = :status WHERE id = :courseID";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':courseID', $courseID, PDO::PARAM_INT);

  return $stmt->execute();
}

public function SELECT_ALL_COURSES(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, course.id AS course_ID FROM course LEFT JOIN departments ON departments.id = course.department_id ");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function insert_Course($department, $course_name){
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO course (department_id, course_name) VALUES (?, ?)");
  $sql->execute([$department, $course_name]);
  
}

public function update_Course($course_name, $course_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE course SET course_name = ? WHERE id = ?");
  $sql->execute([$course_name, $course_id]);
  
}


public function ACTIVATE_course($courseID_activate){
  $connection = $this->getConnection();

  $status = 'Active';

  $sql = $connection->prepare("UPDATE course SET course_status = ? WHERE id = ?");
  $sql->execute([$status, $courseID_activate]);
  
}

public function unactivate_course($courseID){
  $connection = $this->getConnection();

  $status = 'Not Activated';

  $sql = $connection->prepare("UPDATE course SET course_status = ? WHERE id = ?");
  $sql->execute([$status, $courseID]);
  
}

//---------------------------------------------------------------------------------------------------GRAPHS
//-----------------------------------------------------------------------------------------------------
public function Archive_Research_BasedOn_Course(){
  $connection = $this->getConnection();

$stmt = $connection->prepare("SELECT course_name, COUNT(*) as count FROM archive_research LEFT JOIN course ON course.id = archive_research.course_id GROUP BY course_id");
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
return $result;
}

public function Archive_Research_BasedOn_Department(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT dept_code, name, COUNT(*) as count FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id GROUP BY department_id ORDER BY count DESC");
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

public function SELECT(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT dept_code, name, COUNT(*) as count FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id GROUP BY department_id ORDER BY count DESC");
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}

public function Archive_Research_Views_BasedOn_Course(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT course.course_name, archive_research_views.date_of_views, COUNT(*) as count 
  FROM archive_research_views 
  LEFT JOIN archive_research ON archive_research.id = archive_research_views.archive_research_id 
  LEFT JOIN course ON course.id = archive_research.course_id 
  GROUP BY archive_research_views.date_of_views, course.course_name");
  $stmt->execute();
  
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $result;
}


public function Archive_Research_Views_BasedOn_Departments(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT departments.name, archive_research_views.date_of_views, COUNT(*) as count 
  FROM archive_research_views 
  LEFT JOIN archive_research ON archive_research.archive_id = archive_research_views.archive_research_id 
  LEFT JOIN departments ON departments.id = archive_research.department_id WHERE departments.name IS NOT NULL
  GROUP BY archive_research_views.date_of_views, departments.name;");
  $stmt->execute();
  
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
return $result;
}


//----------------------------------------------------------------------------------------------------------STUDENT PAGE
public function student_profile($student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, students_data.id as aid, departments.id as did, course.id as cid FROM students_data 
  LEFT JOIN departments ON departments.id = students_data.department_id 
  LEFT JOIN course ON course.id = students_data.course_id WHERE students_data.id = ? ");
	$stmt->execute([$student_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}

public function student_profile_by_sno($student_no) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, students_data.id as aid FROM students_data 
  LEFT JOIN departments ON departments.id = students_data.department_id 
  LEFT JOIN course ON course.id = students_data.course_id WHERE students_data.student_id = ? ");
	$stmt->execute([$student_no]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}

public function student_profile_by_email($email) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, students_data.id AS studID FROM students_data 
  LEFT JOIN departments ON departments.id = students_data.department_id 
  LEFT JOIN course ON course.id = students_data.course_id WHERE students_data.student_email = ? ");
	$stmt->execute([$email]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}

public function view_profile($student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, students_data.student_id AS studID, archive_research.archive_id AS aid FROM students_data 
  LEFT JOIN departments ON departments.id = students_data.department_id 
  LEFT JOIN course ON course.id = students_data.course_id
  LEFT JOIN archive_research ON archive_research.student_id = students_data.id
  WHERE students_data.student_id = ?");
	$stmt->execute([$student_id]);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
  // Check if the results array is not empty
  if (!empty($results)) {
      // Profile data will be in the first row
      $profile = $results[0];
      
      // Collect works
      $profile['works'] = [];
      foreach ($results as $row) {
          if (!empty($row['aid'])) {
              $profile['works'][] = [
                  'aid' => $row['aid'],
                  'project_title' => $row['project_title']
              ];
          }
      }
      return $profile;
  }

}
public function view_owner_profile($student_email) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, students_data.student_id AS studID, archive_research.archive_id AS aid FROM students_data 
  LEFT JOIN departments ON departments.id = students_data.department_id 
  LEFT JOIN course ON course.id = students_data.course_id
  LEFT JOIN archive_research ON archive_research.student_id = students_data.id
  WHERE students_data.student_email = ?");
	$stmt->execute([$student_email]);
  $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
  // Check if the results array is not empty
  if (!empty($results)) {
      // Profile data will be in the first row
      $profile = $results[0];
      
      // Collect works
      $profile['works'] = [];
      foreach ($results as $row) {
          if (!empty($row['aid'])) {
              $profile['works'][] = [
                  'aid' => $row['aid'],
                  'project_title' => $row['project_title']
              ];
          }
      }
      return $profile;
  }

}
public function student_update_verify_status($verified, $student_id) {
  $connection = $this->getConnection();
  $status = 'Approved';

  $stmt = $connection->prepare("UPDATE students_data SET verify_status=?, school_verify = ? WHERE student_id=?");
  $stmt->execute([$verified, $status, $student_id]);

}
public function student_forgot_account($email, $verification_code) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET verification_code= ? WHERE student_email=?");
  $stmt->execute([$verification_code, $email]);

}
public function recover_code_with_email($email, $verification_code) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET verification_code= ? WHERE student_email=?");
  $stmt->execute([$verification_code, $email]);

}

public function student_change_password($email, $password) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET student_password = ? WHERE student_email=?");
  $stmt->execute([md5($password), $email]);

}
public function student_login_log($student_id, $logs, $date, $time) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("INSERT INTO system_notification(student_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
  $stmt->execute([$student_id, $logs, $date, $time]);

}
public function student_register_select_StudentNumber($studentNumber) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_id = ?");
  $stmt->execute([$studentNumber]);
  $result = $stmt->fetch(); # get user data

  return $result;
}

public function student_register_select_email($email) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_email = ?");
  $stmt->execute([$email]);
  $result = $stmt->fetch(); # get user data

  return $result;
}

public function student_register_INSERT_Info($department, $department_course, $student_number, $first_name, $last_name, $PhoneNumber, $email, $pword, $imagePath, $verification_code) {
  $connection = $this->getConnection();
	
  
  
  $sql = $connection->prepare("INSERT INTO students_data(department_id, course_id, student_id, first_name, last_name, phone_number, student_email, student_password, profile_picture, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $sql->execute([$department, $department_course, $student_number, $first_name, $last_name, $PhoneNumber, $email, md5($pword), $imagePath, $verification_code]);

}



public function studentLogin($email, $password, $redirect_to) {
  session_start();
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_email=? AND student_password= ? ");
  $stmt->execute([$email, md5($password)]);

  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $student_id = $data['id'];
      $student_email = $data['student_email'];
      $student_no = $data['student_id'];
      $department_id = $data['department_id'];
      $course_id = $data['course_id'];
      $pword = $data['student_password'];
      $verifystatus = $data['verify_status'];
      $school_verify = $data['school_verify'];
  }

  if ($pword == md5($password)) {
      if ($verifystatus == 'Not Verified') {
          // Handle account verification if needed
          $_SESSION['alert'] = "Account Verification";
          $_SESSION['status'] = "Verify your Account";
          $_SESSION['status-code'] = "info";
          header("location: ../student/student_verify_account.php?student_no=$student_no");
      } else if ($school_verify == 'For Approval') {
        // Handle account verification if needed
        $_SESSION['alert'] = "Account Confirmation";
        $_SESSION['status'] = "Wait for admin approve your account";
        $_SESSION['status-code'] = "info";
        header("location: ../student/login.php");
    } else {
          // Handle successful login
          date_default_timezone_set('Asia/Manila');
          $date = date('F / d l / Y');
          $time = date('g:i A');
          $logs = 'You successfully logged in to your account.';

          $sql = $connection->prepare("INSERT INTO system_notification(student_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
          $sql->execute([$student_id, $logs, $date, $time]);

          $_SESSION['auth'] = true;
          $_SESSION['auth_user'] = [
              'student_id' => $student_id,
              'student_email' => $student_email,
              'department_id' => $department_id,
              'course_id' => $course_id,
          ];

          $_SESSION['alert'] = "Success";
          $_SESSION['status'] = "Log In Success";
          $_SESSION['status-code'] = "success";
          
          if (isset($redirect_to) || !empty($redirect_to) || $redirect_to !== '') {
            $redirect_url = urldecode($redirect_to);
            header("location: $redirect_url");
          } 
          if (!isset($redirect_to) || empty($redirect_to)) {
            header("location: ../student/all_project_list.php");
          }
      }
  } else {
      // Handle incorrect login details
      
      $_SESSION['alert'] = "Oppss...";
      $_SESSION['status'] = "Incorrect Log In Details";
      $_SESSION['status-code'] = "info";
      header("location: ../student/login.php");
  }
}



public function UPDATE_student_info_onSETTINGS($fname, $mname, $lname, $department, $course, $student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET first_name=?, middle_name=?, last_name=?, department_id=?, course_id=? WHERE id=?");
  $result = $stmt->execute([$fname, $mname, $lname, $department, $course, $student_id]);

  return $result;

}


public function UPDATE_student_password($npword, $student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET student_password = ? WHERE id=?");
  $result = $stmt->execute([$npword, $student_id]);

  return $result;

}


public function delete_student($student_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("DELETE FROM students_data WHERE id = ?");
  $sql->execute([$student_id]);
  
}

public function update_student_school_verification($student_id){
  $connection = $this->getConnection();

  $school_verify = 'Approved';

  $sql = $connection->prepare("UPDATE students_data SET school_verify = ? WHERE id = ?");
  $sql->execute([$school_verify, $student_id]);
  
}



//NOTIFICATION COUNT
public function student_Insert_NOTIFICATION($student_id, $logs, $date, $time) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO system_notification(student_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
  $success = $sql->execute([$student_id, $logs, $date, $time]);
  return $success;
}


//NOTIFICATION COUNT
public function studentNOTIFICATION_COUNT($studentID, $unread) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) AS total_unread FROM system_notification WHERE student_id = ? AND status = ?");
    $stmt->execute([$studentID, $unread]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUnread = $result['total_unread'];

    echo $totalUnread;

}

//ALL READ, UNREAD NOTIFICATIONS
public function studentNOTIFICATION_Read_Unread($student_id) {
  $connection = $this->getConnection();

      $stmt = $connection->prepare("SELECT * FROM system_notification LEFT JOIN students_data ON students_data.id = system_notification.student_id WHERE system_notification.student_id = ? ORDER BY system_notification.id DESC LIMIT 5");
        $stmt->execute([$student_id]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $notifications; // Add this line to return the notifications

}

//MARK ALL NOTIFICATIONS AS READ
public function studentNOTIFICATION_MarkASRead($read, $student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE system_notification SET status = ? WHERE student_id = ?");
  $stmt->execute([$read, $student_id]);

}


public function SELECT_student_profile($student_id) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("SELECT profile_picture FROM students_data WHERE id = ? ");
  $sql->execute([$student_id]);
  $row = $sql->fetch(PDO::FETCH_ASSOC);
  $result = $row['profile_picture'];

  return $result;

}

public function UPDATE_student_profile($imagePath, $student_id) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE students_data SET profile_picture = ? WHERE id = ?");
  $result = $sql->execute([$imagePath, $student_id]);

  return $result;

}

public function SELECT_ALL_STUDENT_ARCHIVE_RESEARCH($student_email){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID, archive_research.archive_id AS aid FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id WHERE archive_research.research_owner_email = ? ORDER BY dateOFSubmit DESC");
  $stmt->execute([$student_email]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function COUNT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear, $research_date) {
  $connection = $this->getConnection();
  $query = "SELECT COUNT(*) as total FROM archive_research WHERE document_status = 'Accepted'";
  
  if (!empty($searchInput)) {
    $query .= " AND (project_title LIKE :searchInput OR project_abstract LIKE :searchInput OR archive_id LIKE :searchInput)";
  }
  if (!empty($department)) {
      $query .= " AND archive_research.department_id = :department";
  }
  if (!empty($course)) {
    $query .= " AND archive_research.course_id = :course";
  }
  if (!empty($keywords)) {
    $keywordArray = array_map('trim', explode(',', $keywords));
    $keywordConditions = [];

    foreach ($keywordArray as $index => $keyword) {
        $param = ":keyword" . $index;
        $keywordConditions[] = "archive_research.keywords LIKE $param";
    }

    $query .= " AND (" . implode(" OR ", $keywordConditions) . ")";
  }
  if (!empty($fromYear)) {
      $query .= " AND YEAR(date_published) >= :fromYear";
  }
  if (!empty($toYear)) {
      $query .= " AND YEAR(date_published) <= :toYear";
  }
  if (!empty($research_date)) {
    $orderDirection = $research_date === 'newest' ? 'DESC' : 'ASC';
    $query .= " ORDER BY date_published " . $orderDirection;
  }

  
  $stmt = $connection->prepare($query);

  if (!empty($searchInput)) {
      $stmt->bindValue(':searchInput', '%' . $searchInput . '%', PDO::PARAM_STR);
  }
  if (!empty($department)) {
      $stmt->bindValue(':department', $department, PDO::PARAM_STR);
  }
  if (!empty($course)) {
    $stmt->bindValue(':course', $course, PDO::PARAM_STR);
  }
  if (!empty($keywords)) {
    foreach ($keywordArray as $index => $keyword) {
        $param = ":keyword" . $index;
        $stmt->bindValue($param, '%' . trim($keyword) . '%', PDO::PARAM_STR);
    }
  }
  if (!empty($fromYear)) {
      $stmt->bindValue(':fromYear', $fromYear, PDO::PARAM_INT);
  }
  if (!empty($toYear)) {
      $stmt->bindValue(':toYear', $toYear, PDO::PARAM_INT);
  }

  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row['total'];
}
public function SELECT_FILTERED_ARCHIVE_RESEARCH($searchInput, $department, $course, $keywords, $fromYear, $toYear, $research_date, $limit, $offset){
  $connection = $this->getConnection();
  $query = 'SELECT *, archive_research.id AS archiveID FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id WHERE 1=1 AND document_status = "Accepted"';

  if (!empty($searchInput)) {
    $query .= " AND (project_title LIKE :searchInput OR project_abstract LIKE :searchInput OR archive_id LIKE :searchInput)";
  }
  if (!empty($department)) {
      $query .= " AND archive_research.department_id = :department";
  }
  if (!empty($course)) {
    $query .= " AND archive_research.course_id = :course";
  }
  if (!empty($keywords)) {
    $keywordArray = array_map('trim', explode(',', $keywords));
    $keywordConditions = [];

    foreach ($keywordArray as $index => $keyword) {
        $param = ":keyword" . $index;
        $keywordConditions[] = "archive_research.keywords LIKE $param";
    }

    $query .= " AND (" . implode(" OR ", $keywordConditions) . ")";
  }
  if (!empty($fromYear)) {
      $query .= " AND YEAR(date_published) >= :fromYear";
  }
  if (!empty($toYear)) {
      $query .= " AND YEAR(date_published) <= :toYear";
  }
  if (!empty($research_date)) {
    $orderDirection = $research_date === 'newest' ? 'DESC' : 'ASC';
    $query .= " ORDER BY date_published " . $orderDirection;
  }
  if (!empty($limit)) {
    $query .= " LIMIT :limit";
  }
  if (!empty($offset)) {
    $query .= " OFFSET :offset";
  }
  
  $stmt = $connection->prepare($query);

  if (!empty($searchInput)) {
      $stmt->bindValue(':searchInput', '%' . $searchInput . '%', PDO::PARAM_STR);
  }
  if (!empty($department)) {
      $stmt->bindValue(':department', $department, PDO::PARAM_STR);
  }
  if (!empty($course)) {
    $stmt->bindValue(':course', $course, PDO::PARAM_STR);
  }
  if (!empty($keywords)) {
    foreach ($keywordArray as $index => $keyword) {
        $param = ":keyword" . $index;
        $stmt->bindValue($param, '%' . trim($keyword) . '%', PDO::PARAM_STR);
    }
  }
  if (!empty($fromYear)) {
      $stmt->bindValue(':fromYear', $fromYear, PDO::PARAM_INT);
  }
  if (!empty($toYear)) {
      $stmt->bindValue(':toYear', $toYear, PDO::PARAM_INT);
  }
  if (!empty($limit)) {
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
  }
  if (!empty($offset)) {
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  }
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_OWNED_ARCHIVE_RESEARCH($owner_email, $searchInput, $fromYear, $toYear, $documentStatus, $research_date){
  $connection = $this->getConnection();
  $query = 'SELECT *, archive_research.id AS archiveID FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id WHERE 1=1';

  if (!empty($searchInput)) {
    $query .= " AND (project_title LIKE :searchInput OR project_abstract LIKE :searchInput OR archive_id LIKE :searchInput)";
  }
  if (!empty($owner_email)) {
    $query .= " AND research_owner_email = :ownerEmail";
  }
  if (!empty($documentStatus)) {
      $query .= " AND document_status = :documentStatus";
  }
  if (!empty($fromYear) || $fromYear !== '') {
      $query .= " AND YEAR(date_published) >= :fromYear";
  }
  if (!empty($toYear) || $toYear !== '') {
      $query .= " AND YEAR(date_published) <= :toYear";
  }
  if (!empty($research_date)) {
    $orderDirection = $research_date === 'newest' ? 'DESC' : 'ASC';
    $query .= " ORDER BY date_published " . $orderDirection;
  }
  
  $stmt = $connection->prepare($query);

  if (!empty($searchInput)) {
      $stmt->bindValue(':searchInput', '%' . $searchInput . '%', PDO::PARAM_STR);
  }
  if (!empty($owner_email)) {
    $stmt->bindValue(':ownerEmail', $owner_email, PDO::PARAM_STR);
  }
  if (!empty($documentStatus)) {
      $stmt->bindValue(':documentStatus', $documentStatus , PDO::PARAM_STR);
  }
  if (!empty($fromYear) || $fromYear !== '') {
      $stmt->bindValue(':fromYear', $fromYear, PDO::PARAM_INT);
  }
  if (!empty($toYear) || $toYear !== '') {
      $stmt->bindValue(':toYear', $toYear, PDO::PARAM_INT);
  }
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------








}

?>