<?php
session_start();
include("db.php");

// Fetch current election settings
$query = "SELECT * FROM election_control WHERE id=1";
$result = mysqli_query($conn,$query);
$row = mysqli_fetch_assoc($result);

if(isset($_POST['update'])){

    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $status = $_POST['status'];

    $update = "UPDATE election_control 
               SET start_time='$start',
               end_time='$end',
               status='$status'
               WHERE id=1";

    mysqli_query($conn,$update);

    echo "<script>alert('Election settings updated');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Election Control</title>

<style>
body{
font-family:Arial;
background:#f2f2f2;
}

.container{
width:40%;
margin:80px auto;
background:white;
padding:30px;
border-radius:10px;
box-shadow:0 0 10px gray;
}

h2{
text-align:center;
}

input,select{
width:100%;
padding:10px;
margin-top:10px;
margin-bottom:20px;
}

button{
padding:10px;
width:100%;
background:#1d2671;
color:white;
border:none;
}
</style>

</head>

<body>

<div class="container">

<h2>Election Control Panel</h2>

<form method="post">

<label>Start Time</label>
<input type="datetime-local" name="start_time" required>

<label>End Time</label>
<input type="datetime-local" name="end_time" required>

<label>Status</label>
<select name="status">
<option value="active">Active</option>
<option value="inactive">Inactive</option>
</select>

<button name="update">Update Election</button>

</form>

</div>

</body>
</html>