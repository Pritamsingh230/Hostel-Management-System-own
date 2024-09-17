<?php
if (isset($_POST['login-submit'])) {

    require 'config.inc.php';

    // Retrieve and sanitize input data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['pwd'];

    // Check if inputs are empty
    if (empty($username) || empty($password)) {
        header("Location: ../login-hostel_manager.php?error=emptyfields");
        exit();
    } else {
        // Prepare and execute SQL query
        $sql = "SELECT * FROM Hostel_Manager WHERE Username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            header("Location: ../login-hostel_manager.php?error=sqlerror");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $row['Pwd'])) {
                // Start session and set session variables
                session_start();
                $_SESSION['hostel_man_id'] = $row['Hostel_man_id'];
                $_SESSION['fname'] = $row['Fname'];
                $_SESSION['lname'] = $row['Lname'];
                $_SESSION['mob_no'] = $row['Mob_no'];
                $_SESSION['username'] = $row['Username'];
                $_SESSION['hostel_id'] = $row['Hostel_id'];
                $_SESSION['email'] = $row['Email'];
                $_SESSION['isadmin'] = $row['Isadmin'];

                // Redirect based on user role
                if ($_SESSION['isadmin'] =
