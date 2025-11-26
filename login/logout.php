<?php
session_start();

// Destroy session
$_SESSION = array();
session_destroy();

// Redirect to login page with a logout message
header("Location: login.php?message=logged_out");
exit();
?>

