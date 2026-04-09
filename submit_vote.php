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
$check = mysqli_query($conn, "
SELECT * FROM votes WHERE username='$username'
");

if(mysqli_num_rows($check) > 0){
    echo "You have already voted!";
    exit();
}


// insert vote
$query = "
INSERT INTO votes (candidate_id, username)
VALUES ('$candidate_id', '$username')
";

if(mysqli_query($conn,$query)){
    echo "<h2>Vote submitted successfully!</h2>";
    echo "<br><a href='results.php'>See Results</a>";
}
else{
    echo "Error submitting vote";
}
?>