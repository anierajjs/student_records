<?php
session_start();
require_once 'config/db_connect.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page
header('Location: index.php');
exit();
?>
