<?php
include("db.php");

$election = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM election_settings LIMIT 1"));

$start_time = strtotime($election['start_time']);
$end_time = strtotime($election['end_time']);
$current_time = time();

if($current_time >= $start_time && $current_time <= $end_time){
    echo "Votes cannot be reset during the election.";
    exit();
}

mysqli_query($conn,"DELETE FROM votes");

header("Location: admin_dashboard.php");
exit();
?>