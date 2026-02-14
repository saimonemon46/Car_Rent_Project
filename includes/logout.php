<?php
session_start();

// Clear ALL session data, not just specific keys
session_unset();
session_destroy();

// Redirect to home page
header("Location: ../index.php");
exit();
?>