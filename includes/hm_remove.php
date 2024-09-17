<?php
// Start the session
session_start();

// Check if the form has been submitted
if (isset($_POST['hm_remove_submit'])) {

    // Include database connection configuration
    require 'config.inc.php';

    // Retrieve and sanitize input data
    $username = mysqli_real_escape_string($conn, $_POST['hm_uname']);
    $hostel_name = mysqli_real_escape_string($conn, $_POST['hostel_name']);
    $adminPassword = $_POST['pass'];

    // Validate input
    if (empty($username) || empty($hostel_name) || empty($adminPassword)) {
        header("Location: ../admin/create_hm.php?error=emptyfields");
        exit();
    } else {
        // Check if the Hostel Manager exists
        $sql = "SELECT * FROM Hostel_Manager WHERE Username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                // Check if the Hostel exists
                $sql2 = "SELECT * FROM Hostel WHERE Hostel_name = ?";
                $stmt2 = mysqli_prepare($conn, $sql2);

                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, "s", $hostel_name);
                    mysqli_stmt_execute($stmt2);
                    $result2 = mysqli_stmt_get_result($stmt2);

                    if ($row2 = mysqli_fetch_assoc($result2)) {
                        $HNO = $row2['Hostel_id'];

                        if ($HNO == $row['Hostel_id']) {
                            // Verify admin password
                            $pwdCheck = password_verify($adminPassword, $_SESSION['PSWD']);
                            if (!$pwdCheck) {
                                header("Location: ../admin/create_hm.php?error=wrongpwd");
                                exit();
                            } else {
                                // Delete the Hostel Manager
                                $sql3 = "DELETE FROM Hostel_Manager WHERE Username = ?";
                                $stmt3 = mysqli_prepare($conn, $sql3);

                                if ($stmt3) {
                                    mysqli_stmt_bind_param($stmt3, "s", $username);
                                    $result3 = mysqli_stmt_execute($stmt3);

                                    if ($result3) {
                                        header("Location: ../admin/create_hm.php?DeletionSuccessful");
                                    } else {
                                        header("Location: ../admin/create_hm.php?error=DeletionFailed");
                                    }
                                    exit();
                                } else {
                                    header("Location: ../admin/create_hm.php?error=sqlerror");
                                    exit();
                                }
                            }
                        } else {
                            header("Location: ../admin/create_hm.php?error=wronghostel");
                            exit();
                        }
                    } else {
                        header("Location: ../admin/create_hm.php?error=nohostel");
                        exit();
                    }
                } else {
                    header("Location: ../admin/create_hm.php?error=sqlerror");
                    exit();
                }
            } else {
                header("Location: ../admin/create_hm.php?error=nouser");
                exit();
            }
            mysqli_stmt_close($stmt);
        } else {
            header("Location: ../admin/create_hm.php?error=sqlerror");
            exit();
        }
    }

} else {
    header("Location: ../index.php");
    exit();
}

// Close the database connection
mysqli_close($conn);
?>
