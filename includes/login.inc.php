<?php
if (isset($_POST['login-submit'])) {

    require 'config.inc.php';

    // Retrieve and sanitize input data
    $roll = $_POST['student_roll_no'];
    $password = $_POST['pwd'];

    // Check if inputs are empty
    if (empty($roll) || empty($password)) {
        header("Location: ../index.php?error=emptyfields");
        exit();
    } else {
        // Prepare SQL query to prevent SQL injection
        $sql = "SELECT * FROM Student WHERE Student_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            // Handle SQL error
            header("Location: ../index.php?error=sqlerror");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $roll);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if a row was returned
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $row['Pwd'])) {
                // Start session and set session variables
                session_start();
                $_SESSION['roll'] = $row['Student_id'];
                $_SESSION['fname'] = $row['Fname'];
                $_SESSION['lname'] = $row['Lname'];
                $_SESSION['mob_no'] = $row['Mob_no'];
                $_SESSION['department'] = $row['Dept'];
                $_SESSION['year_of_study'] = $row['Year_of_study'];
                $_SESSION['hostel_id'] = $row['Hostel_id'];
                $_SESSION['room_id'] = $row['Room_id'];

                // Redirect to home page
                header("Location: ../home.php?login=success");
                exit();
            } else {
                header("Location: ../index.php?error=wrongpwd");
                exit();
            }
        } else {
            header("Location: ../index.php?error=nouser");
            exit();
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($conn);
} else {
    // Redirect if form was not submitted
    header("Location: ../index.php");
    exit();
}
?>
