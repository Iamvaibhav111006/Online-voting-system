<?php
session_start();
include("db.php");

// check login
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// check candidate selected
if(!isset($_POST['candidate'])){
    echo "Error: No candidate selected!";
    exit();
}

$candidate_id = $_POST['candidate'];
$username = $_SESSION['username'];


// OPTIONAL: check if user already voted
$stmt = mysqli_prepare($conn, "SELECT * FROM votes WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$check = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($check) > 0){
    echo "You have already voted!";
    exit();
}


// insert vote
$stmt = mysqli_prepare($conn, "INSERT INTO votes (candidate_id, username) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $candidate_id, $username);
$result = mysqli_stmt_execute($stmt);

if($result){
    echo "<h2>Vote submitted successfully!</h2>";
    echo "<br><a href='results.php'>See Results</a>";
}
else{
    echo "Error submitting vote";
}
?>