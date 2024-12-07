<?php 

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Database {
  private $servername = "localhost";
  private $username = "root";
  private $password = "";
  private $database = "research_repository";
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
  public function admin_register_select_email_and_own_email($email, $own_email, $admin_id) {
    $connection = $this->getConnection();

    $stmt = $connection->prepare("SELECT * FROM admin_account WHERE admin_email != ? AND admin_email = ? AND id != ?");
    $stmt->execute([$email, $own_email, $admin_id]);
    $result = $stmt->fetch(); # get user data

    return $result;
}
   public function admin_register_select_email($email, $admin_id) {
    $connection = $this->getConnection();

    $stmt = $connection->prepare("SELECT * FROM admin_account WHERE admin_email = ? AND id != ?");
    $stmt->execute([$email, $admin_id]);
    $result = $stmt->fetch(); # get user data

    return $result;
}
public function admin_select_email($email) {
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
    $stmt->execute([$email, md5($password)]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
      return json_encode(array('status_code' => 'info', 'status' => 'Incorrect Log In Details', 'alert' => 'Oppss...'));
    }

    $admin_id = $data['id'];
    $admin_UNIQUEid = $data['uniqueID'];
    $admin_email = $data['admin_email']; 
    $pword = $data['admin_password'];
    $verifystatus = $data['verify_status'];
    $admin_status = $data['admin_status'];
    $role_id = $data['role_id'];

    if ($pword == md5($password) && $admin_status == 'Active') {
      if ($verifystatus == 'Not Verified') {
        $_SESSION['email'] = $admin_email;
        return json_encode(array('status_code' => 'info', 'status' => 'Please verify your account first', 'alert' => 'Account Verification', 'redirect' => 'admin_verify_account.php?id='.$admin_id));    
      } else {
        // Handle successful login
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You logged in.';
        $online_offline_status = 'Online';

        $userRole = $this->getRoleById($role_id);
        if ($userRole) {
          $role_status = $userRole['role_status'];
        } else {
          return json_encode(array('status_code' => 'info', 'status' => 'Please contact the administrator for assistance.', 'alert' => 'Role Inactive'));
        }
        
        if ($role_status != 'Active') {
            return json_encode(array('status_code' => 'info', 'status' => 'Please contact the administrator for assistance.', 'alert' => 'Role Inactive'));
        }
        
        // Handle login by inserting logs and updating status
        $sql = $connection->prepare("INSERT INTO admin_systemnotification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
        $sql->execute([$admin_id, $logs, $date, $time]);
        
        $sql2 = $connection->prepare("UPDATE admin_account SET online_offlineStatus = ? WHERE id = ?");
        $sql2->execute([$online_offline_status, $admin_id]);
        
        // Set session variables
        $_SESSION['auth'] = true;
        $_SESSION['auth_user'] = [
            'admin_id' => $admin_id,
            'admin_uniqueID' => $admin_UNIQUEid,
            'admin_email' => $admin_email,
            'role_id' => $role_id
        ];
        
        return json_encode(array('status_code' => 'success', 'status' => 'Log in success', 'alert' => 'Success'));
        
        // Check permissions and redirect
        $permissions = explode(',', $userRole['permissions']);
        
        function hasPermit($permissions, $permissionToCheck) {
            foreach ($permissions as $permission) {
                if (strpos($permission, $permissionToCheck) === 0) {
                    return true;
                }
            }
            return false;
        }
        
        // Determine redirect URL based on permissions
        if ($_SESSION['auth_user']['admin_id'] == 0) {
            echo "<script>window.location.href='../adminsystem/index.php'</script>";
            exit();
        } elseif (hasPermit($permissions, 'dashboard_view')) {
            header("Location: ../adminsystem/dashboard.php");
        } elseif (hasPermit($permissions, 'student_list_view')) {
            header("Location: ../adminsystem/students.php");
        } elseif (hasPermit($permissions, 'research_view')) {
            header("Location: ../adminsystem/all_project_list.php");
        } elseif (hasPermit($permissions, 'department_view')) {
            header("Location: ../adminsystem/departments.php");
        } elseif (hasPermit($permissions, 'course_view')) {
            header("Location: ../adminsystem/courses.php");
        } elseif (hasPermit($permissions, 'role_view')) {
            header("Location: ../adminsystem/roles.php");
        } elseif (hasPermit($permissions, 'user_view')) {
            header("Location: ../adminsystem/admins.php");
        } else {
            header('Location: ../bad-request.php');
        }
        exit();
      }
    } elseif($admin_status == 'Inactive') {
        return json_encode(array('status_code' => 'info', 'status' => 'Sorry, but your account is now inactive.', 'alert' => 'Oppss...'));
    } else {
        return json_encode(array('status_code' => 'info', 'status' => 'Incorrect Log In Details', 'alert' => 'Oppss...'));
    }
}
public function admin_profile_by_id($admin_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM admin_account WHERE id = ?");
  $stmt->execute([$admin_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
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

  $stmt = $connection->prepare("SELECT
      COUNT(admin_systemnotification.id) AS total_unread,
      admin_systemnotification.*,
      admin_account.*,
      archive_research.archive_id,
      archive_research.project_title,
      COALESCE(admin_account.id, archive_research.archive_id) as source_id,
      CASE 
        WHEN admin_account.id IS NOT NULL THEN 'admin'
        WHEN archive_research.archive_id IS NOT NULL THEN 'research'
      END as notification_type
    FROM admin_systemnotification
    LEFT JOIN admin_account 
      ON admin_account.id = admin_systemnotification.admin_id
    LEFT JOIN archive_research 
      ON archive_research.archive_id = admin_systemnotification.admin_id
    WHERE admin_systemnotification.admin_id = ?
    AND admin_systemnotification.status = ?
      OR archive_research.archive_id IS NOT NULL
    ORDER BY admin_systemnotification.id DESC");
    $stmt->execute([$adminID, $unread]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalUnread = $result['total_unread'];

    echo $totalUnread;

}


//ALL READ, UNREAD NOTIFICATIONS
public function adminsystemNOTIFICATION_Read_Unread($coordinatorID) {
  $connection = $this->getConnection();

      $stmt = $connection->prepare("SELECT 
      admin_systemnotification.*,
      admin_account.*,
      archive_research.archive_id,
      archive_research.project_title,
      COALESCE(admin_account.id, archive_research.archive_id) as source_id,
      CASE 
        WHEN admin_account.id IS NOT NULL THEN 'admin'
        WHEN archive_research.archive_id IS NOT NULL THEN 'research'
      END as notification_type
    FROM admin_systemnotification
    LEFT JOIN admin_account 
      ON admin_account.id = admin_systemnotification.admin_id
    LEFT JOIN archive_research 
      ON archive_research.archive_id = admin_systemnotification.admin_id
    WHERE admin_systemnotification.admin_id = ?
      OR archive_research.archive_id IS NOT NULL
    ORDER BY admin_systemnotification.id DESC");
        $stmt->execute([$coordinatorID]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $notifications;

}

//MARK ALL NOTIFICATIONS AS READ
public function adminsystemNOTIFICATION_MarkASRead($read, $admin_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_systemnotification SET status = ? WHERE admin_id = ?");
  $stmt->execute([$read, $admin_id]);

  $stmt2 = $connection->prepare("UPDATE admin_systemnotification asn 
    INNER JOIN archive_research ar ON ar.archive_id = asn.admin_id 
    SET asn.status = ? 
    WHERE ar.archive_id IS NOT NULL");
  $stmt2->execute([$read]);

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

public function role_profile($roleID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM roles WHERE id = ?");
	$stmt->execute([$roleID]);
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


public function UPDATE_admin_info_onSETTINGS($fname, $mname, $lname,  $cp_number, $adminID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_account SET first_name=?, middle_name=?, last_name=?, phone_number=? WHERE id=?");
  $result = $stmt->execute([$fname, $mname, $lname, $cp_number, $adminID]);

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
  $stmt = $connection->prepare("SELECT *, admin_account.id AS adminID FROM admin_account LEFT JOIN roles ON roles.id = admin_account.role_id WHERE admin_account.id != ? AND admin_account.delete_flag = 0");
  $stmt->execute([$admin_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function showRoles(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, roles.id AS roleID, roles.description AS role_description FROM roles LEFT JOIN departments ON departments.id = roles.department_id WHERE roles.id != 1 ORDER BY roles.id DESC");
  $stmt->execute();
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
public function showDepartments_WHERE_ACTIVE_PER_DEPARTMENT($departmentId){
  $connection = $this->getConnection();
  $status = 'Active';

  $stmt = $connection->prepare("SELECT * FROM departments WHERE department_status = ? AND id = ?");
  $stmt->execute([$status, $departmentId]);
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
public function SELECT_ALL_AdminsData(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare(
    "SELECT * FROM admin_account LEFT JOIN roles ON roles.id = admin_account.role_id WHERE admin_account.delete_flag = 0 ORDER BY roles.id ASC");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_ALL_RolesData(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare(
    "SELECT * FROM roles WHERE delete_flag = 0 AND id != 1 ORDER BY id ASC");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_ALL_StudentsData(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare(
    "SELECT 
      students_data.*, 
      students_data.id AS studID, 
      departments.id AS department_id,
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

public function SELECT_ALL_StudentsData_WHERE_DEPARTMENT_IS($departmentId){
  $connection = $this->getConnection();
  $stmt = $connection->prepare(
    "SELECT 
      students_data.*, 
      students_data.id AS studID, 
      departments.id AS department_id,
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
    WHERE 
        students_data.department_id = ?
    GROUP BY 
        students_data.id"); 
  $stmt->execute([$departmentId]);
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
  LEFT JOIN plagiarism_summary ON plagiarism_summary.archive_id = archive_research.id GROUP BY archive_research.archive_id ORDER BY archive_research.id DESC");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_ALL_ARCHIVE_RESEARCH_WHERE_DEPARTMENT_IS($department){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID, archive_research.archive_id as aid FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id 
  LEFT JOIN plagiarism_summary ON plagiarism_summary.archive_id = archive_research.id
  WHERE archive_research.department_id = ? GROUP BY archive_research.archive_id ORDER BY archive_research.id DESC");
  $stmt->execute([$department]);
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
public function SELECT_ALL_OWNED_ARCHIVE_RESEARCH($owner_email, $limit, $offset) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID,
    (SELECT COUNT(id) FROM archive_research_views WHERE archive_research_views.archive_research_id = archive_research.archive_id) AS view_count
    FROM archive_research 
    LEFT JOIN departments ON departments.id = archive_research.department_id
    LEFT JOIN course ON course.id = archive_research.course_id 
    WHERE archive_research.research_owner_email = :owner_email
    ORDER BY view_count DESC 
    LIMIT :limit OFFSET :offset");

  $stmt->bindParam(':owner_email', $owner_email, PDO::PARAM_STR);
  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
  $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function INSERT_DOWNLOAD_LOGS($user_id, $archive_id, $date) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("INSERT INTO admin_download_logs (user_id, archive_id, download_date) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $archive_id, $date]);

}
public function INSERT_STUDENT_DOWNLOAD_LOGS($user_id, $archive_id, $date) {
  $connection = $this->getConnection();
  
  $stmt = $connection->prepare("INSERT INTO student_download_logs (student_id, archive_id, download_date) VALUES (?, ?, ?)");
  $stmt->execute([$user_id, $archive_id, $date]);

  if ($stmt){
    date_default_timezone_set('Asia/Manila');
    $date = date('F / d l / Y');
    $time = date('g:i A');
    $logs = 'You downloaded a PDF file.';
    
    $sql = $connection->prepare("INSERT INTO system_notification(student_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
    $sql->execute([$user_id, $logs, $date, $time]);
  }

}

public function SELECT_RESEARCH_OWNER_EMAIL($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT research_owner_email as email, students_data.id as studentID, archive_research.project_title as project_title FROM students_data LEFT JOIN archive_research ON research_owner_email = students_data.student_email WHERE archive_research.id = ?;");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
  $status = 'Rejected';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE document_status = ? ");
  $stmt->execute([$status]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research WHERE 1=1");
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_BY_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research WHERE department_id = ?");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_WHERE_PUBLISH_BY_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research WHERE document_status = 'Accepted' AND department_id = ?");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_RECENT_RESEARCH_PAPER(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT archive_research.date_published, archive_research.dateOFSubmit, archive_research.page_count, archive_research.project_title as project_title, archive_research.archive_id as aid, archive_research.id as ari FROM archive_research WHERE archive_research.document_status = 'Accepted' ORDER BY ari DESC LIMIT 5;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_RECENT_RESEARCH_PAPER_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT archive_research.date_published, archive_research.dateOFSubmit, archive_research.page_count, archive_research.project_title as project_title, archive_research.archive_id as aid, archive_research.id as ari FROM archive_research WHERE archive_research.document_status = 'Accepted' AND archive_research.department_id = ? ORDER BY ari DESC LIMIT 5;");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}


public function SELECT_ALL_PUBLISHED_RESEARCH_PAPER(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT archive_research.date_published, archive_research.dateOFSubmit, archive_research.page_count, archive_research.project_title as project_title, archive_research.archive_id as aid, archive_research.id as ari FROM archive_research WHERE archive_research.document_status = 'Accepted' ORDER BY ari DESC");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_ALL_PUBLISHED_RESEARCH_PAPER_BY_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT archive_research.date_published, archive_research.dateOFSubmit, archive_research.page_count, archive_research.project_title as project_title, archive_research.archive_id as aid, archive_research.id as ari FROM archive_research WHERE archive_research.document_status = 'Accepted' AND archive_research.department_id = ? ORDER BY ari DESC");
  $stmt->execute([$departmentId]);
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
public function SELECT_TOP_RESEARCH_CONTRIBUTOR_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, COUNT(*) as count, students_data.student_id as studID FROM archive_research LEFT JOIN students_data ON student_email = research_owner_email WHERE archive_research.department_id = ? GROUP BY archive_research.research_owner_email ORDER BY count DESC LIMIT 5;");
    $stmt->execute([$departmentId]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_ALL_PLAGIARIZED_RESEARCH_CONTENT(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, SUM(plagiarism_percentage) as total_percentage, plagiarism_summary.archive_id as plagiarism_id, archive_research.archive_id as aid FROM plagiarism_summary LEFT JOIN archive_research ON archive_research.id = plagiarism_summary.archive_id GROUP BY plagiarism_summary.archive_id ORDER BY plagiarism_summary.id DESC;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_PLAGIARIZED_RESEARCH_CONTENT(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, SUM(plagiarism_percentage) as total_percentage, plagiarism_summary.archive_id as plagiarism_id, archive_research.archive_id as aid FROM plagiarism_summary LEFT JOIN archive_research ON archive_research.id = plagiarism_summary.archive_id GROUP BY plagiarism_summary.archive_id ORDER BY plagiarism_summary.id DESC LIMIT 5;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_PLAGIARIZED_RESEARCH_CONTENT_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, SUM(plagiarism_percentage) as total_percentage, plagiarism_summary.archive_id as plagiarism_id, archive_research.archive_id as aid FROM plagiarism_summary LEFT JOIN archive_research ON archive_research.id = plagiarism_summary.archive_id WHERE archive_research.department_id = ? GROUP BY plagiarism_summary.archive_id ORDER BY plagiarism_summary.id DESC LIMIT 5;");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_PLAGIARIZED_RESEARCH_CONTENT_OF($archiveID){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, plagiarism_summary.archive_id as plagiarism_id, archive_research.archive_id as aid FROM plagiarism_summary LEFT JOIN archive_research ON archive_research.id = plagiarism_summary.similar_archive_id WHERE plagiarism_summary.archive_id = ? ORDER BY plagiarism_summary.id DESC;");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_PROJECT_TITLE_WHERE_ARCHIVE_ID($archiveID){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT project_title FROM archive_research WHERE id = ? ");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_TOTAL_PERCENTAGE_PLAGIARISM_WHERE_ARCHIVE_ID($archiveID){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT SUM(plagiarism_percentage) as total_percentage  FROM plagiarism_summary WHERE archive_id = ? ");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_TOP_10_VIEWS_RESEARCH_PAPER(){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count, archive_research.project_title as project_title, archive_research.archive_id as aid FROM archive_research LEFT JOIN archive_research_views ON archive_research.archive_id = archive_research_views.archive_research_id GROUP BY archive_research_id ORDER BY count DESC LIMIT 10;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_TOP_10_VIEWS_RESEARCH_PAPER_BY_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count, archive_research.project_title as project_title, archive_research.archive_id as aid FROM archive_research LEFT JOIN archive_research_views ON archive_research.archive_id = archive_research_views.archive_research_id WHERE archive_research.department_id = ? GROUP BY archive_research_id ORDER BY count DESC LIMIT 10;");
  $stmt->execute([$departmentId]);
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

public function SELECT_RESEARCH_PUBLISHED_PER_WEEK_BY_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT date_published, COUNT(id) as count FROM `archive_research` WHERE document_status = 'Accepted' AND department_id = ? GROUP BY WEEK(date_published) ORDER BY date_published ASC LIMIT 5");
  $stmt->execute([$departmentId]);
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

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research WHERE document_status = 'Rejected'");
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
public function SELECT_DEPARTMENT_BY_NAME($department_name, $exclude_id){
  $connection = $this->getConnection();
  if($exclude_id != ''){
    $stmt = $connection->prepare("SELECT * FROM departments WHERE name = ? AND id != ?");
    $stmt->execute([$department_name, $exclude_id]);
  }else{
    $stmt = $connection->prepare("SELECT * FROM departments WHERE name = ?");
    $stmt->execute([$department_name]);
  }
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_DEPARTMENT_BY_CODE($department_code, $exclude_id){
  $connection = $this->getConnection();
  if($exclude_id != ''){
    $stmt = $connection->prepare("SELECT * FROM departments WHERE dept_code = ? AND id != ?");
    $stmt->execute([$department_code, $exclude_id]);
  }else{
    $stmt = $connection->prepare("SELECT * FROM departments WHERE dept_code = ?");
    $stmt->execute([$department_code]);
  }
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COURSE_BY_NAME($course_name){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM course WHERE course_name = ?");
  $stmt->execute([$course_name]);
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

public function SELECT_COUNT_ALL_COURSES_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();
  
  $stmt = $connection->prepare("SELECT COUNT(*) as total_count FROM course WHERE department_id = ?");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_ACTIVE_COURSES_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM course WHERE course_status = 'Active' AND department_id = ?");
  $stmt->execute([$departmentId]);
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

public function SELECT_COUNT_ACCEPTED_STUDENTS_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();
  
  $stmt = $connection->prepare("SELECT COUNT(*) as total_count FROM students_data WHERE department_id = ?");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_COUNT_ALL_ACTIVE_STUDENTS_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM students_data WHERE department_id = ? AND school_verify = 'Approved'");
  $stmt->execute([$departmentId]);
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
    students_data.first_name as fname,
    students_data.middle_name as mname,
    students_data.last_name as lname,
    departments.*,
    course.*,
    SUM(plagiarism_summary.plagiarism_percentage) AS total_percentage,
    (SELECT COUNT(id) FROM archive_research_views WHERE archive_research_views.archive_research_id = archive_research.archive_id) AS view_count
    FROM archive_research
    LEFT JOIN departments ON departments.id = archive_research.department_id
    LEFT JOIN course ON course.id = archive_research.course_id
    LEFT JOIN students_data ON students_data.id = archive_research.student_id
	LEFT JOIN plagiarism_summary ON archive_research.id = plagiarism_summary.archive_id
    WHERE archive_research.archive_id = ?");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_UPLOADED_ADMIN_ARCHIVE_RESEARCH($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT 
    archive_research.*,
    archive_research.archive_id AS archiveID,
    archive_research.id AS aid,
    admin_account.uniqueID AS sid,
    admin_account.first_name as fname,
    admin_account.middle_name as mname,
    admin_account.last_name as lname,
    departments.*,
    course.*,
    (SELECT COUNT(id) FROM archive_research_views WHERE archive_research_views.archive_research_id = archive_research.archive_id) AS view_count
    FROM archive_research
    LEFT JOIN departments ON departments.id = archive_research.department_id
    LEFT JOIN course ON course.id = archive_research.course_id
    LEFT JOIN admin_account ON admin_account.uniqueID = archive_research.student_id
    WHERE archive_research.archive_id = ?");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_PLAGIARISM_RESULTS_RESEARCH($archiveID, $similar_archive_id){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM plagiarism_results WHERE plagiarism_results.archive_id = ? AND similar_archive_id = ? ORDER BY id DESC");
  $stmt->execute([$archiveID, $similar_archive_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_PLAGIARISM_SUMMARY_RESEARCH($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, COUNT(plagiarism_summary.id) as total_ids, SUM(plagiarism_percentage) as total_percentage FROM plagiarism_summary LEFT JOIN archive_research ON plagiarism_summary.archive_id = archive_research.id WHERE plagiarism_summary.archive_id = ? GROUP BY plagiarism_summary.archive_id;");
  $stmt->execute([$archiveID]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

public function SELECT_SUMMARY_PLAGIARISM_RESULTS_RESEARCH($archiveID){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, plagiarism_summary.archive_id AS plaid, plagiarism_summary.similar_archive_id as sai FROM plagiarism_summary LEFT JOIN archive_research ON plagiarism_summary.similar_archive_id = archive_research.id  WHERE plagiarism_summary.archive_id = ? ORDER BY archive_research.dateOFSubmit DESC");
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
  $status = 'Rejected';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE document_status = ? AND research_owner_email = ?");
  $stmt->execute([$status, $student_email]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function SELECT_COUNT_ALL_ARCHIVE_RESEARCH_FROM_STUDENT($student_email){
  $connection = $this->getConnection();
  $status = 'Rejected';

  $stmt = $connection->prepare("SELECT COUNT(*) FROM archive_research WHERE research_owner_email = ?");
  $stmt->execute([$student_email]);
  $result = $stmt->fetchColumn();

  return $result;
}

public function check_if_archive_already_viewed($archive_research_id, $student_id){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT COUNT(*) as count FROM archive_research_views WHERE archive_research_id = ? AND student_id = ?");
  $stmt->execute([$archive_research_id, $student_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}
public function insert_Research_Views($archive_research_id, $student_id, $date){
  $connection = $this->getConnection();
  $sql = $connection->prepare("INSERT INTO archive_research_views(archive_research_id, student_id, date_of_views) VALUES (?, ?, ?)");
  $sql->execute([$archive_research_id, $student_id, $date]);
  
}


public function SELECT_ALL_STUDENTS_DATA(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM students_data");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
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

public function insert_Admin($uniqueId, $first_name, $last_name, $complete_address, $phone_number, $email, $password, $role_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO admin_account(uniqueID, first_name, last_name, complete_address, phone_number, admin_email, admin_password, role_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  
  return $sql->execute([$uniqueId, $first_name, $last_name, $complete_address, $phone_number, $email, md5($password), $role_id]);
  
}
public function update_Admin($first_name, $last_name, $complete_address, $phone_number, $email, $password, $role_id, $id){
  $connection = $this->getConnection();

  if (empty($password)) {
    $sql = $connection->prepare("UPDATE admin_account SET first_name = ?, last_name = ?, complete_address = ?, phone_number = ?, admin_email = ?, role_id = ? WHERE id = ?");
    return $sql->execute([$first_name, $last_name, $complete_address, $phone_number, $email, $role_id, $id]);
  } else {
    $sql = $connection->prepare("UPDATE admin_account SET first_name = ?, last_name = ?, complete_address = ?, phone_number = ?, admin_email = ?, admin_password = ?, role_id = ? WHERE id = ?");
    return $sql->execute([$first_name, $last_name, $complete_address, $phone_number, $email, md5($password), $role_id, $id]);
  }
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

  $status = 'Inactive';

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
      $sql = $connection->prepare("SELECT documents FROM archive_research WHERE id = ?");
      $sql->execute([$archive_id]);
      $result = $sql->fetch(PDO::FETCH_ASSOC);
      $filePath = $result['documents'];
      if (file_exists($filePath)) { 
          unlink($filePath);
      }

      $sql = $connection->prepare("DELETE FROM archive_research WHERE id = ?");
      $sql->execute([$archive_id]);
      
      return true;
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

public function UPDATE_ARCHIVE_RESEARCH_WHERE_STUDENT_ID($student_id, $department, $course){
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE archive_research SET department_id = ?, course_id = ? WHERE student_id = ?");
  $sql->execute([$department, $course, $student_id]);
  
}
public function unpublish_research($archiveID){
  $connection = $this->getConnection();
  
  $date = '';
  $status = 'Rejected';

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
  $connection = $this->getConnection();
  $query = "UPDATE departments SET department_status = :status WHERE id = :departmentID";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':departmentID', $departmentID, PDO::PARAM_INT);
  $stmt->execute();

  $stmt1 = $connection->prepare("SELECT * FROM departments WHERE id = ?");
  $stmt1->execute([$departmentID]);
  $fetch = $stmt1->fetch(PDO::FETCH_ASSOC);
  return $fetch;
}
public function update_admin_status($adminID, $status) {
  $connection = $this->getConnection();
  $query = "UPDATE admin_account SET admin_status = :status WHERE id = :adminID AND id != 1";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':adminID', $adminID, PDO::PARAM_INT);
  $stmt->execute();

  $stmt1 = $connection->prepare("SELECT * FROM admin_account WHERE id = ?");
  $stmt1->execute([$adminID]);
  $fetch = $stmt1->fetch(PDO::FETCH_ASSOC);
  return $fetch;
}
public function update_Role_status($roleID, $status) {
  $connection = $this->getConnection();
  $query = "UPDATE roles SET role_status = :status WHERE id = :roleID AND id != 1";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':roleID', $roleID, PDO::PARAM_INT);
  $stmt->execute();

  $stmt1 = $connection->prepare("SELECT * FROM roles WHERE id = ?");
  $stmt1->execute([$roleID]);
  $fetch = $stmt1->fetch(PDO::FETCH_ASSOC);
  return $fetch;
}
public function update_course_status($courseID, $status) {
  $connection = $this->getConnection();
  $query = "UPDATE course SET course_status = :status WHERE id = :courseID";
  $stmt = $this->conn->prepare($query);
  
  $stmt->bindParam(':status', $status, PDO::PARAM_STR);
  $stmt->bindParam(':courseID', $courseID, PDO::PARAM_INT);
  $stmt->execute();

  $stmt1 = $connection->prepare("SELECT * FROM course WHERE id = ?");
  $stmt1->execute([$courseID]);
  $fetch = $stmt1->fetch(PDO::FETCH_ASSOC);
  return $fetch;
}

public function insert_Role($role_name, $description, $department, $permissions) {
  try {
      $connection = $this->getConnection();

      $sql = $connection->prepare("INSERT INTO roles(role_name, description, department_id, permissions) VALUES (?, ?, ?, ?)");
      $result = $sql->execute([$role_name, $description, $department, $permissions]);
      
      return $result;
  } catch (PDOException $e) {
      error_log("Error inserting role: " . $e->getMessage());
      return false;
  }
}
public function update_Role($role_id, $role_name, $description, $department_id, $permissions) {
  try {
      $stmt = $this->conn->prepare("UPDATE roles SET 
          role_name = ?, 
          description = ?, 
          department_id = ?, 
          permissions = ? 
          WHERE id = ?");
          
      $stmt->execute([$role_name, $description, $department_id, $permissions, $role_id]);
      return true;
  } catch(PDOException $e) {
      error_log("Failed to update role: " . $e->getMessage());
      return false;
  }
}
public function getRoleById($roleId) {
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM roles WHERE id = ?");
  $stmt->execute([$roleId]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function getDepartmentById($departmentId) {
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT * FROM departments WHERE id = ?");
  $stmt->execute([$departmentId]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function SELECT_ALL_COURSES(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, course.id AS course_ID FROM course LEFT JOIN departments ON departments.id = course.department_id ");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function SELECT_ALL_COURSES_PER_DEPARTMENT($departmentId) {
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, course.id AS course_ID FROM course LEFT JOIN departments ON departments.id = course.department_id WHERE departments.id = ?");
  $stmt->execute([$departmentId]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function research_profile($archive_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM archive_research WHERE id = ? ");
	$stmt->execute([$archive_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}
public function course_profile($course_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM course WHERE id = ? ");
	$stmt->execute([$course_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}
public function department_profile($department_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM departments WHERE id = ? ");
	$stmt->execute([$department_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;

}
public function insert_Course($department, $course_name){
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO course (department_id, course_name) VALUES (?, ?)");
  $sql->execute([$department, $course_name]);
  
}

public function update_Course($course_name, $department_id, $course_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE course SET course_name = ?, department_id = ? WHERE id = ?");
  $sql->execute([$course_name, $department_id, $course_id]);
  
}


public function ACTIVATE_course($courseID_activate){
  $connection = $this->getConnection();

  $status = 'Active';

  $sql = $connection->prepare("UPDATE course SET course_status = ? WHERE id = ?");
  $sql->execute([$status, $courseID_activate]);
  
}

public function unactivate_course($courseID){
  $connection = $this->getConnection();

  $status = 'Inactive';

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
public function Archive_Research_BasedOn_Course_WHERE_DEPARTMENT($departmentId){
  $connection = $this->getConnection();

$stmt = $connection->prepare("SELECT course_name as name, dept_code as dept_code, course.id as id, COUNT(*) as count FROM archive_research LEFT JOIN course ON course.id = archive_research.course_id LEFT JOIN departments ON course.department_id = departments.id WHERE departments.id = ? GROUP BY course_id;");
$stmt->execute([$departmentId]);

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
public function Research_BasedOn_Department_AND_Status(){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT 
    dept_code, 
    name, 
    COUNT(CASE WHEN archive_research.document_status = 'Accepted' THEN 1 END) AS accepted_count, 
    COUNT(CASE WHEN archive_research.document_status = 'Rejected' THEN 1 END) AS rejected_count
FROM 
    archive_research 
LEFT JOIN 
    departments 
ON 
    departments.id = archive_research.department_id 
GROUP BY 
    department_id, dept_code, name 
ORDER BY 
    accepted_count DESC;");
  $stmt->execute();

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}
public function Research_BasedOn_Department_Course($departmentId){
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT 
    course_name as name, 
    dept_code, 
    course.id as id, 
    COUNT(CASE WHEN archive_research.document_status = 'Accepted' THEN 1 END) AS accepted_count, 
    COUNT(CASE WHEN archive_research.document_status = 'Rejected' THEN 1 END) AS rejected_count
FROM 
    archive_research 
LEFT JOIN 
    course
ON 
    course.id = archive_research.course_id 
LEFT JOIN
    departments
ON
    departments.id = course.department_id
WHERE
    departments.id = ?
GROUP BY 
    course_id, dept_code, course_name 
ORDER BY 
    accepted_count DESC;");
  $stmt->execute([$departmentId]);

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
  GROUP BY departments.name;");
  $stmt->execute();
  
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
return $result;
}
public function Archive_Research_Views_BasedOn_Department($departmentId){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT departments.dept_code, course.course_name as name, archive_research_views.date_of_views, COUNT(archive_research_views.archive_research_id) as count 
  FROM archive_research_views 
  LEFT JOIN archive_research ON archive_research.archive_id = archive_research_views.archive_research_id 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id
  WHERE departments.name IS NOT NULL AND departments.id = ?
  GROUP BY course.course_name;");
  $stmt->execute([$departmentId]);
  
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
return $result;
}
public function UPDATE_STUDENT_INFO($student_id,$first_name, $last_name, $phonenumber, $email, $password, $department, $course){
  $connection = $this->getConnection();

  if (empty($password)) {
    $stmt = $connection->prepare("UPDATE students_data SET first_name = ?, last_name = ?, phone_number = ?, student_email = ?, department_id = ?, course_id = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $phonenumber, $email, $department, $course, $student_id]);
  } else {
    $stmt = $connection->prepare("UPDATE students_data SET first_name = ?, last_name = ?, phone_number = ?, student_email = ?, student_password = ?, department_id = ?, course_id = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $phonenumber, $email, md5($password), $department, $course, $student_id]);
  }
  
  // Get updated student data
  $stmt1 = $connection->prepare("SELECT * FROM students_data WHERE id = ?");
  $stmt1->execute([$student_id]);
  $result = $stmt1->fetch(PDO::FETCH_ASSOC);
  
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

public function view_plagiarism_history($student_id){
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT *, archive_research.archive_id AS aid FROM plagiarism_summary LEFT JOIN archive_research ON archive_research.id = plagiarism_summary.archive_id LEFT JOIN students_data ON archive_research.student_id = students_data.id WHERE students_data.student_id = ?");
  $stmt->execute([$student_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
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
public function admin_recover_code_with_email($email, $verification_code) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_account SET verification_code= ? WHERE admin_email=?");
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
public function get_admin_name_by_id($admin_id) {
  $stmt = $this->conn->prepare("SELECT * FROM admin_account WHERE id = ?");
  $stmt->execute([$admin_id]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if ($result) {
    return $result['first_name'] . ' ' . $result['last_name'];
  } else {
    return 'User not found';
  }
}
public function admin_activity_log($admin_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM admin_account 
                                LEFT JOIN admin_systemnotification ON admin_systemnotification.admin_id = admin_account.id 
                                WHERE admin_account.id = ? 
                                ORDER BY admin_systemnotification.id DESC");
  $stmt->execute([$admin_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function admin_all_activity_log($admin_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM admin_account LEFT JOIN admin_systemnotification ON admin_systemnotification.admin_id = admin_account.id WHERE admin_id != ? ORDER BY admin_systemnotification.id DESC;");
  $stmt->execute([$admin_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function student_activity_log($student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM system_notification WHERE student_id = ? ORDER BY id DESC");
  $stmt->execute([$student_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
public function student_register_select_StudentNumber($studentNumber) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_id = ?");
  $stmt->execute([$studentNumber]);
  $result = $stmt->fetch(); # get user data

  return $result;
}
public function check_student_email($email, $studID) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_email = ? AND id != ?");
  $stmt->execute([$email, $studID]);
  $result = $stmt->fetch();

  return $result;
}
public function student_register_select_email($email) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_email = ?");
  $stmt->execute([$email]);
  $result = $stmt->fetch(); # get user data

  return $result;
}
public function student_forgot_select_email($email) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_email = ?");
  $stmt->execute([$email]);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($result) {
    $_SESSION['email'] = $email;
  }

  return $result;
}
public function student_register_INSERT_Info($department, $department_course, $student_number, $first_name, $last_name, $PhoneNumber, $email, $pword, $imagePath, $verification_code) {
  $connection = $this->getConnection();
	
  
  
  $sql = $connection->prepare("INSERT INTO students_data(department_id, course_id, student_id, first_name, last_name, phone_number, student_email, student_password, profile_picture, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $sql->execute([$department, $department_course, $student_number, $first_name, $last_name, $PhoneNumber, $email, md5($pword), NULL, $verification_code]);

}
public function SELECT_ACCOUNT_INBOX($student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM archive_research WHERE student_id = ? ORDER BY id DESC");
  $stmt->execute([$student_id]);
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}    
public function SELECT_ADMIN_INBOX() {
  $connection = $this->getConnection();

    $stmt = $connection->prepare("SELECT *, admin_systemnotification.id as asnid, admin_systemnotification.status as inbox_status, archive_research.id as arid
                                FROM admin_systemnotification
                                LEFT JOIN archive_research ON admin_systemnotification.admin_id = archive_research.archive_id
                                WHERE archive_research.archive_id = admin_systemnotification.admin_id;");
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}  
public function SELECT_ACCOUNT_INBOX_WHERE($searchInbox) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM archive_research WHERE project_title LIKE :searchInbox");
  if (!empty($searchInbox)) {
    $stmt->bindValue(':searchInbox', '%'. $searchInbox. '%', PDO::PARAM_STR);
  }
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}    

public function studentLogin($email, $password, $redirect_to) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("SELECT * FROM students_data WHERE student_email=?");
  $stmt->execute([$email]);

  while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $student_id = $data['id'];
      $student_name = $data['first_name'].' '.$data['last_name'];
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
        $_SESSION['email'] = $student_email;
        return json_encode(array('status_code' => 'info', 'status' => 'Please verify your account first', 'alert' => 'Account Verification', 'redirect' => 'student_verify_account.php?student_no='.$student_no));    
      } else if ($school_verify == 'For Approval') {
        // Handle account verification if needed
          return json_encode(array('status_code' => 'info', 'status' => 'Wait for admin approve your account', 'alert' => 'Oppss...'));
      } else if ($school_verify == 'Blocked') {
        // Handle account verification if needed
          return json_encode(array('status_code' => 'info', 'status' => 'Sorry, but your account has been blocked', 'alert' => 'Account Blocked'));
      } else {
          // Handle successful login
          date_default_timezone_set('Asia/Manila');
          $date = date('F / d l / Y');
          $time = date('g:i A');
          $logs = 'You logged in.';

          $sql = $connection->prepare("INSERT INTO system_notification(student_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
          $sql->execute([$student_id, $logs, $date, $time]);

          $_SESSION['auth'] = true;
          $_SESSION['auth_user'] = [
              'student_id' => $student_id,
              'student_name' => $student_name,
              'student_no' => $student_no,
              'student_email' => $student_email,
              'department_id' => $department_id,
              'course_id' => $course_id,
          ];


          if (isset($redirect_to) || !empty($redirect_to) || $redirect_to !== '') {
            return json_encode(array('status_code' => 'success', 'status' => 'Login successful', 'alert' => 'Success', 'redirect' => $redirect_to));
          } 
          if (!isset($redirect_to) || empty($redirect_to)) {
            return json_encode(array('status_code' => 'success', 'status' => 'Login successful', 'alert' => 'Success', 'redirect' => '../student/all_project_list.php'));
          }
      }
  } else {
      return json_encode(array('status_code' => 'info', 'status' => 'Incorrect log in details', 'alert' => 'Oppss...'));
  }
}



public function UPDATE_student_info_onSETTINGS($fname, $mname, $lname, $department, $course, $student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET first_name=?, middle_name=?, last_name=?, department_id=?, course_id=? WHERE id=?");
  $result = $stmt->execute([$fname, $mname, $lname, $department, $course, $student_id]);
  
  if($result){
    $stmt1 = $connection->prepare("SELECT * FROM students_data WHERE student_id = ?");
    $stmt1->execute([$student_id]);
    $data = $stmt1->fetch(PDO::FETCH_ASSOC);
    
    return $data;
  }
}


public function UPDATE_student_password($npword, $student_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE students_data SET student_password = ? WHERE id=?");
  $result = $stmt->execute([$npword, $student_id]);

  return $result;

}
public function delete_department($department_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("DELETE FROM departments WHERE id = ?");
  $sql->execute([$department_id]);
  
}
public function delete_course($course_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("DELETE FROM course WHERE id = ?");
  $sql->execute([$course_id]);
  
}
public function delete_admin($admin_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE admin_account SET delete_flag = 1 WHERE id = ?");
  $sql->execute([$admin_id]);
  
}
public function delete_role($role_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("DELETE FROM roles WHERE id = ?");
  $sql->execute([$role_id]);
  
}

public function delete_student($student_id){
  $connection = $this->getConnection();

  $sql = $connection->prepare("DELETE FROM students_data WHERE id = ?");
  $sql->execute([$student_id]);
  
}

public function block_student_school_verification($student_id, $set_blocked){
  $connection = $this->getConnection();

  $sql = $connection->prepare("UPDATE students_data SET school_verify = ? WHERE id = ?");
  $sql->execute([$set_blocked, $student_id]);

  $stmt1 = $connection->prepare("SELECT * FROM students_data WHERE id = ?");
  $stmt1->execute([$student_id]);
  $fetch = $stmt1->fetch(PDO::FETCH_ASSOC);
  return $fetch;
}
public function update_student_school_verification($student_id){
  $connection = $this->getConnection();

  $school_verify = 'Approved';

  $sql = $connection->prepare("UPDATE students_data SET school_verify = ? WHERE id = ?");
  $sql->execute([$school_verify, $student_id]);

  $stmt1 = $connection->prepare("SELECT * FROM students_data WHERE id = ?");
  $stmt1->execute([$student_id]);
  $fetch = $stmt1->fetch(PDO::FETCH_ASSOC);
  return $fetch;
  
}

public function admin_Insert_NOTIFICATION($admin_id, $logs, $date, $time) {
  $connection = $this->getConnection();

  $sql = $connection->prepare("INSERT INTO admin_systemnotification(admin_id, logs, logs_date, logs_time) VALUES (?, ?, ?, ?)");
  $success = $sql->execute([$admin_id, $logs, $date, $time]);
  return $success;
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


public function studentINBOX_MarkASRead($read, $archive_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE archive_research SET inbox_read = ? WHERE archive_id = ?");
  return $stmt->execute([$read, $archive_id]);

}
public function adminINBOX_MarkASRead($read, $archive_id) {
  $connection = $this->getConnection();

  $stmt = $connection->prepare("UPDATE admin_systemnotification SET status = ? WHERE admin_id = ?");
  return $stmt->execute([$read, $archive_id]);

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

public function SELECT_ALL_STUDENT_ARCHIVE_RESEARCH($owner_email, $limit, $offset) {
  $connection = $this->getConnection();
  $stmt = $connection->prepare("SELECT *, archive_research.id AS archiveID, archive_research.archive_id AS aid FROM archive_research 
  LEFT JOIN departments ON departments.id = archive_research.department_id
  LEFT JOIN course ON course.id = archive_research.course_id WHERE archive_research.research_owner_email = :owner_email ORDER BY archiveID DESC LIMIT :limit OFFSET :offset");
   
  $stmt->bindParam(':owner_email', $owner_email, PDO::PARAM_STR);
  $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
  $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
  
  $stmt->execute();
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

public function COUNT_FILTERED_OWNED_ARCHIVE_RESEARCH($owner_email, $searchInput, $keywords, $documentStatus, $fromYear, $toYear, $research_date) {
  $connection = $this->getConnection();
  $query = "SELECT COUNT(*) as total FROM archive_research WHERE 1 = 1";
  
  if (!empty($searchInput)) {
    $query .= " AND (project_title LIKE :searchInput OR project_abstract LIKE :searchInput OR archive_id LIKE :searchInput)";
  }
  if (!empty($owner_email)) {
    $query .= " AND research_owner_email = :ownerEmail";
  }
  if (!empty($documentStatus)) {
    $query .= " AND document_status = :documentStatus";
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
      $query .= " AND YEAR(dateOFSubmit) >= :fromYear";
  }
  if (!empty($toYear)) {
      $query .= " AND YEAR(dateOFSubmit) <= :toYear";
  }
  if (!empty($research_date)) {
    $orderDirection = $research_date === 'newest' ? 'DESC' : 'ASC';
    $query .= " ORDER BY dateOFSubmit " . $orderDirection;
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

public function SELECT_OWNED_ARCHIVE_RESEARCH($owner_email, $searchInput, $keywords, $fromYear, $toYear, $documentStatus, $research_date, $limit, $offset){
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
      $query .= " AND YEAR(dateOFSubmit) >= :fromYear";
  }
  if (!empty($toYear) || $toYear !== '') {
      $query .= " AND YEAR(dateOFSubmit) <= :toYear";
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
  if (!empty($research_date)) {
    $orderDirection = $research_date === 'newest' ? 'DESC' : 'ASC';
    $query .= " ORDER BY archive_research.id " . $orderDirection;
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
  if (!empty($owner_email)) {
    $stmt->bindValue(':ownerEmail', $owner_email, PDO::PARAM_STR);
  }
  if (!empty($documentStatus)) {
      $stmt->bindValue(':documentStatus', $documentStatus , PDO::PARAM_STR);
  }
  if (!empty($keywords)) {
    foreach ($keywordArray as $index => $keyword) {
        $param = ":keyword" . $index;
        $stmt->bindValue($param, '%' . trim($keyword) . '%', PDO::PARAM_STR);
    }
  }
  if (!empty($fromYear) || $fromYear !== '') {
      $stmt->bindValue(':fromYear', $fromYear, PDO::PARAM_INT);
  }
  if (!empty($toYear) || $toYear !== '') {
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
//-------------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------------

}

?>