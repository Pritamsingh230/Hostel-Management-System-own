<?php
if (isset($_POST['hm_signup_submit'])) {

    require 'config.inc.php';

    // Retrieve and sanitize input data
    $username = mysqli_real_escape_string($conn, $_POST['hm_uname']);
    $fname = mysqli_real_escape_string($conn, $_POST['hm_fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['hm_lname']);
    $mobile = mysqli_real_escape_string($conn, $_POST['hm_mobile']);
    $hostel_name = mysqli_real_escape_string($conn, $_POST['hostel_name']);
    $email = mysqli_real_escape_string($conn, $_POST['Email']);
    $password = $_POST['pass'];
    $cnfpassword = $_POST['confpass'];

    // Validate input
    if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
        header("Location: ../admin/create_hm.php?error=invalidusername");
        exit();
    } else if ($password !== $cnfpassword) {
        header("Location: ../admin/create_hm.php?error=passwordcheck");
        exit();
    } else {
        // Check if username already exists
        $sql = "SELECT Username FROM Hostel_Manager WHERE Username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            header("Location: ../admin/create_hm.php?error=sqlerror");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultCheck = mysqli_stmt_num_rows($stmt);

        if ($resultCheck > 0) {
            header("Location: ../admin/create_hm.php?error=userexists");
            exit();
        } else {
            // Hash the password
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);

            // Get Hostel ID
            $sql = "SELECT Hostel_id FROM Hostel WHERE Hostel_name = ?";
            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                header("Location: ../admin/create_hm.php?error=sqlerror");
                exit();
            }

            mysqli_stmt_bind_param($stmt, "s", $hostel_name);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $HostelID);

            if (mysqli_stmt_fetch($stmt)) {
                // Insert new Hostel Manager
                $zz = 0; // Assuming Isadmin is a boolean or integer flag
                $sql = "INSERT INTO Hostel_Manager (Username, Fname, Lname, Mob_no, Hostel_id, Email, Pwd, Isadmin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if (!$stmt) {
                    header("Location: ../admin/create_hm.php?error=sqlerror");
                    exit();
                }

                mysqli_stmt_bind_param($stmt, "ssssssis", $username, $fname, $lname, $mobile, $HostelID, $email, $hashedPwd, $zz);
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    header("Location: ../admin/create_hm.php?added=success");
                } else {
                    header("Location: ../admin/create_hm.php?added=failure");
                }
                exit();
            } else {
                header("Location: ../admin/create_hm.php?error=nohostel");
                exit();
            }
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} else {
    header("Location: ../admin/create_hm.php");
    exit();
}
?>
