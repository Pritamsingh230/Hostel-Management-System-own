<?php

session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session

// Redirect to the homepage
header("Location: ../index.php");
exit(); // Ensure that no further code is executed

?>
