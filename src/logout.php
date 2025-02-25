<?php
// Start session
session_start();

// Unset only the logged-in user's session variable
unset($_SESSION['LoggedStudent']); // Adjust this to match your session variable

// Redirect to the login page
header("Location: login.php");
exit;
?>