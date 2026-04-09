<?php
session_start();

// mark user as verified
$_SESSION['face_verified'] = true;

// redirect to voting page
header("Location: vote.php");
exit();
?>