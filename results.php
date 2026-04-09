<?php
session_start();
include("db.php");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>TEST 123</title>

<style>
body{
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#1e3c72,#2a5298);
    margin:0;
    padding:0;
    text-align:center;
    color:white;
}

.container{
    width:75%;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 15px 40px rgba(0,0,0,0.3);
}

h2{
    color:#222;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
}

th{
    background:#0f2027;
    color:white;
    padding:14px;
    font-size:16px;
}

td{
    padding:12px;
    border-bottom:1px solid #ddd;
    color:#333;
    font-weight:500;
}

tr:nth-child(even){
    background:#f4f6f8;
}

tr:hover{
    background:#dfe9f3;
    transition:0.3s;
}

.total{
    margin-top:25px;
    font-size:20px;
    color:#222;
    font-weight:bold;
}

.winner{
    margin-top:20px;
    padding:15px;
    background:linear-gradient(45deg,#f7971e,#ffd200);
    color:black;
    font-size:22px;
    font-weight:bold;
    border-radius:8px;
    display:inline-block;
}

.back{
    display:inline-block;
    margin-top:25px;
    padding:12px 25px;
    background:#141e30;
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-weight:bold;
}

.back:hover{
    background:#243b55;
}
</style>
</head>
<body>
<div class="container">

<h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

<a href="logout.php" style="float:right;margin:10px;padding:5px 10px;background:#f44336;color:white;text-decoration:none;">
Logout</a>

<?php
$query = "
SELECT 
    candidates.id, 
    candidates.name, 
    candidates.party,
    COUNT(votes.id) AS total_votes
FROM candidates
LEFT JOIN votes ON candidates.id = votes.candidate_id
GROUP BY candidates.id, candidates.name, candidates.party
";
$result = mysqli_query($conn, $query);

if(!$result){
    die(mysqli_error($conn));
}
echo "<pre>";
print_r(mysqli_fetch_assoc($result));
echo "</pre>";
exit;
// total votes
$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM votes");
$total_row = mysqli_fetch_assoc($total_query);
$total_votes = $total_row['total'];

// highest vote count
$max_query = mysqli_query($conn,"
SELECT MAX(vote_count) AS max_votes FROM (
    SELECT COUNT(votes.id) AS vote_count
    FROM candidates
    LEFT JOIN votes ON candidates.id = votes.candidate_id
    GROUP BY candidates.id
) AS counts
");
$max_row = mysqli_fetch_assoc($max_query);
$max_votes = $max_row['max_votes'];

// get candidates with highest votes
$winner_query = mysqli_query($conn,"
SELECT candidates.name, COUNT(votes.id) AS total_votes
FROM candidates
LEFT JOIN votes ON candidates.id = votes.candidate_id
GROUP BY candidates.id
HAVING total_votes = $max_votes
");
?>

<h2>Voting Results</h2>

<table>
<tr>
    <th>Candidate</th>
    <th>party</th>
    <th>Total Votes</th>
</tr>

<?php
while($row = mysqli_fetch_assoc($result)){
    echo "<tr>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['party_name']."</td>";
    echo "<td>".$row['total_votes']."</td>";
    echo "</tr>";
}
?>
</table>

<h3 class="total">Total Votes: <?php echo $total_votes; ?></h3>

<div class="winner">
<?php
if($total_votes == 0){
    echo "No votes have been cast yet.";
}
else{
    $winner_count = mysqli_num_rows($winner_query);

    if($winner_count == 1){
        $winner = mysqli_fetch_assoc($winner_query);
        echo "Winner: ".$winner['name']." 🎉 (".$winner['total_votes']." votes)";
    }
    else{
        echo "Result: Tie between candidates 🤝";
    }
}
?>
</div>

<a class="back" href="vote.php">Back to Voting</a>

<br><br>
</div>
</body>
</html>