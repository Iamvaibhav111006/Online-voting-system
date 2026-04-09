<?php
session_start();
session_destroy();  // Ends the session and logs out the user
header("Location: admin_login.php");  // Redirects to login page
exit;
?>