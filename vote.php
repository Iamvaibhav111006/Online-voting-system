<?php
session_start();
include("db.php");

// if user not logged in → go to login page
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

// fetch candidates
$result = mysqli_query($conn, "SELECT * FROM candidates");
?>

<!DOCTYPE html>
<html>
<head>
<title>Vote - Online Voting System</title>

<style>
body {
    background: linear-gradient(135deg, #f6d365, #fda085);
    font-family: 'Segoe UI', sans-serif;
}

.vote-card {
    width: 420px;
    margin: 80px auto;
    padding: 35px;
    border-radius: 18px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 35px rgba(0,0,0,0.25);
    text-align: center;
    color: white;
}

.welcome-text {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 10px;
}

.vote-card label {
    display:block;
    padding:10px;
    margin:8px 0;
    border-radius:10px;
    background:rgba(255,255,255,0.12);
    cursor:pointer;
}

.vote-card label:hover{
    background:rgba(255,255,255,0.25);
}

.vote-card button{
    margin-top:15px;
    padding:12px 30px;
    border:none;
    border-radius:12px;
    background:linear-gradient(45deg,#ff416c,#ff4b2b);
    color:white;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
}

.candidate-name{
    font-weight:600;
    font-size:18px;
}

.party-name{
    margin-left:12px;
    color:#222;
    font-size:15px;
}
.table-header, .table-row {
    display: grid;
    grid-template-columns: 80px 1fr 1fr;
    align-items: center;
    padding: 10px;
    margin: 8px 0;
    border-radius: 10px;
}

.table-header {
    font-weight: 700;
    background: rgba(255,255,255,0.25);
}

.table-row {
    background: rgba(255,255,255,0.12);
}

.table-row:hover {
    background: rgba(255,255,255,0.25);
}

.party-name {
    color: #fff;
}
</style>
</head>

<body>

<div class="vote-card">

<h2 class="welcome-text">
Welcome, <?php echo $_SESSION['username']; ?> 👋
</h2>

<h3>Select your candidate</h3>

<form method="POST" action="submit_vote.php">

<div class="table-header">
    <div>Select</div>
    <div>Candidate</div>
    <div>Party</div>
</div>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
    <div class="table-row">
        <div>
            <input type="radio" name="candidate" value="<?php echo $row['id']; ?>" required>
        </div>

        <div class="candidate-name">
            <?php echo $row['name']; ?>
        </div>

        <div class="party-name">
            <?php echo $row['party']; ?>
        </div>
    </div>
<?php } ?>

<button type="submit">Vote</button>

</form>

<br>
<a href="results.php">See Results</a> |
<a href="logout.php">Logout</a>

</div>

</body>
</html>