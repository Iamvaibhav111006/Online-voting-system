<?php
session_start();
include("db.php");

// Only admin can reset
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

// Delete ALL votes
mysqli_query($conn,"DELETE FROM votes");

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit();
?>