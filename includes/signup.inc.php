<?php

if (isset($_POST['signup-submit'])) {

    require 'config.inc.php';

    // Retrieve and sanitize input data
    $roll = mysqli_real_escape_string($conn, $_POST['student_roll_no']);
    $fname = mysqli_real_escape_string($conn, $_POST['student_fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['student_lname']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $dept = mysqli_real_escape_string($conn, $_POST['department']);
    $year = mysqli_real_escape_string($conn, $_POST['year_of_study']);
    $password = $_POST['pwd'];
    $cnfpassword = $_POST['confirmpwd'];

    // Validate input data
    if (!preg_match("/^[a-zA-Z0-9]*$/", $roll)) {
        header("Location: ../signup.php?error=invalidroll");
        exit();
    } else if ($password !== $cnfpassword) {
        header("Location: ../signup.php?error=passwordcheck");
        exit();
    } else {
        // Check if user already exists
        $sql = "SELECT Student_id FROM Student WHERE Student_id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $roll);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultCheck = mysqli_stmt_num_rows($stmt);
            if ($resultCheck > 0) {
                header("Location: ../signup.php?error=userexists");
                exit();
            } else {
                // Insert new user
                $sql = "INSERT INTO Student (Student_id, Fname, Lname, Mob_no, Dept, Year_of_study, Pwd) VALUES (?, ?, ?, ?, ?, ?, ?)";
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ../signup.php?error=sqlerror");
                    exit();
                } else {
                    $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, "sssssss", $roll, $fname, $lname, $mobile, $dept, $year, $hashedPwd);
                    mysqli_stmt_execute($stmt);
                    header("Location: ../index.php?signup=success");
                    exit();
                }
            }
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    header("Location: ../signup.php");
    exit();
}

?>
