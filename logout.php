<?php
session_start();

// Unset all session variables
unset($_SESSION['login']);
unset($_SESSION['admin']);

// Destroy the session
session_destroy();

// Redirect to home page
header("Location: index.php");
exit();
?>
