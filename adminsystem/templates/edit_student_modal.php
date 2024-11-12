<?php 
if (isset($_POST[$uniquePrefix.'update'])){
    $student_id = $_POST['student_id'];
    $first_name = $_POST['firstname'];
    $last_name = $_POST['lastname'];
    $phonenumber = $_POST['pnumber'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $department = $_POST['department'];
    $course = $_POST['course'];

    $stmt = $db->UPDATE_STUDENT_INFO($student_id, $first_name, $last_name, $phonenumber, $email, $password, $department, $course);
    if ($stmt) {    
        date_default_timezone_set('Asia/Manila');
        $date = date('F / d l / Y');
        $time = date('g:i A');
        $logs = 'You updated '.$stmt['first_name'].' '.$stmt['last_name'].'’s information.';
        $stmt = $db->admin_Insert_NOTIFICATION($admin_id, $logs, $date, $time);

        $_SESSION['alert'] = "Success";
        $_SESSION['status'] = "Student information updated.";
        $_SESSION['status-code'] = "success";
        echo "<script>window.location.href = 'student_list.php'</script>";
    } else {
        echo "<script>alert('Failed to update student information.');</script>";
    }
}

?>

<div class="modal fade" id="<?= $uniquePrefix ?>edit_modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit student information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-xl-6">
                            <div class="form-input">
                                <input type="number"  name="student_id" style="display: none;" value="<?= $result['studID'] ?>" readonly>
                                <label for="<?= $uniquePrefix ?>firstname">First name</label>
                                <input type="name" name="firstname" id="<?= $uniquePrefix ?>firstname" value="<?= $result['first_name'] ?>" required>
                                <span id="<?= $uniquePrefix ?>firstname-error" class="error-message" style="color: #a33333;"></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-6">
                            <div class="form-input">
                                <label for="<?= $uniquePrefix ?>lastname">Last name</label>
                                <input type="name" name="lastname" id="<?= $uniquePrefix ?>lastname" value="<?= $result['last_name'] ?>" required>
                                <span id="<?= $uniquePrefix ?>lastname-error" class="error-message" style="color: #a33333;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-xl-6">
                            <div class="form-input">
                                <label for="<?= $uniquePrefix ?>email">Email</label>
                                <input type="email" name="email" id="<?= $uniquePrefix ?>email" value="<?= $result['student_email'] ?>" required>
                                <span id="<?= $uniquePrefix ?>email-error" class="error-message" style="color: #a33333;"></span>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-xl-6">
                            <div class="form-input">
                                <label for="<?= $uniquePrefix ?>pnumber">Phone Number</label>
                                <input type="number" name="pnumber" id="<?= $uniquePrefix ?>pnumber" value="<?= $result['phone_number'] ?>" required>
                                <span id="<?= $uniquePrefix ?>pnumber-error" class="error-message" style="color: #a33333;"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-xl-6">
                            <div class="form-input">
                                <label for="<?= $uniquePrefix ?>department">Department</label>
                                <select name="department" id="<?= $uniquePrefix ?>department" required>
                                    <?php 
                                        $res = $db->showDepartments_WHERE_ACTIVE();

                                        foreach ($res as $item) {
                                            echo '<option value="'.$item['id'].'" '.($item['id'] == $result['department_id'] ? "selected" : "").'>'.$item['name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-xl-6">
                            <div class="form-input">
                                <label for="<?= $uniquePrefix ?>course">Course</label>
                                <select name="course" id="<?= $uniquePrefix ?>course" required>
                                    <option value="<?= $result['course_id'] ?>" selected ><?= $result['course_name'] ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-xl-6">
                            <div class="form-input">
                                <label for="<?= $uniquePrefix ?>password">Password</label>
                                <input type="password" name="password" id="<?= $uniquePrefix ?>password" minlength="8" value="<?= $result['student_password'] ?>" required>
                                <span id="<?= $uniquePrefix ?>password-error" class="error-message m-t-2">Use 8 or more characters with a mix of letters, numbers, & symbols</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button name="<?= $uniquePrefix ?>update" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="css/lib/sweetalert/sweetalert.css" rel="stylesheet">
<script>
    $("#<?= $uniquePrefix ?>department").change(function() {
        var departmentId = $(this).val();
        if(departmentId != " ") {
            $.ajax({
                url: "show_course.php",
                method: "POST",
                data: {"send_department_set": 1, "send_department": departmentId},
                success: function(data) {
                    $("#<?= $uniquePrefix ?>course").html(data).css("display", "block");
                },
                error: function(xhr, status, error) {
                    console.log("AJAX error:", error);
                }
            });
        } else {
            $("#<?= $uniquePrefix ?>course").css("display", "none");
        }
    });
<?php 
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {

?>
<script>
sweetAlert("<?php echo $_SESSION['alert']; ?>", "<?php echo $_SESSION['status']; ?>", "<?php echo $_SESSION['status-code']; ?>");
</script>
<?php
unset($_SESSION['status']);
}
?>
</script>