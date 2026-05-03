<?php
session_start();
include("db.php");

date_default_timezone_set("Asia/Kolkata");

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

if(!isset($_SESSION['face_verified'])){
    header("Location: face_verify.php");
    exit();
}

$election = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM election_settings LIMIT 1"));

$start_time = strtotime($election['start_time']);
$end_time = strtotime($election['end_time']);
$current_time = time();

if($current_time < $start_time){
    echo "<h2 style='text-align:center;color:red;'>Election has not started yet.</h2>";
    exit();
}

if($current_time > $end_time){
    echo "<h2 style='text-align:center;color:red;'>Election is over.</h2>";
    echo "<center><a href='results.php'>See Results</a></center>";
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM candidates");

if(isset($_POST['candidate'])){

    $candidate_id = $_POST['candidate'];
    $username = $_SESSION['username'];

    $check = mysqli_query($conn,"SELECT * FROM votes WHERE username='$username'");
    $check_vote = mysqli_query($conn, "SELECT * FROM votes WHERE username='".$_SESSION['username']."'");

    if(mysqli_num_rows($check_vote) > 0){
        echo "<h2 style='text-align:center;color:green;'>You have already voted.</h2>";
        echo "<p style='text-align:center;'>Results will be shown after the election ends.</p>";
        exit();
    }

    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('You have already voted!');</script>";
    }else{
        $candidate_id = $_POST['candidate'];
        $username = $_SESSION['username'];

        mysqli_query($conn,"INSERT INTO votes (candidate_id, username)
        VALUES ('$candidate_id', '$username')");

        echo "<script>
        alert('Vote submitted successfully');
        window.location='vote.php';
        </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vote - Online Voting System</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#f5f7fb;
    --panel:#ffffff;
    --panel-soft:#f8fbff;
    --text:#172033;
    --muted:#6a7590;
    --line:#dce5f2;
    --primary:#1d4ed8;
    --primary-soft:#e8f0ff;
    --success:#0f766e;
    --success-soft:#e7f8f5;
    --danger:#dc2626;
    --shadow:0 22px 48px rgba(31, 55, 98, 0.10);
}

*{ box-sizing:border-box; }

body{
    margin:0;
    min-height:100vh;
    font-family:"IBM Plex Sans", sans-serif;
    color:var(--text);
    background:
        linear-gradient(180deg, #eef3fb 0%, #f7f9fc 100%);
}

.layout{
    min-height:100vh;
    display:grid;
    grid-template-columns:280px minmax(0, 1fr);
}

.sidebar{
    background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    border-right:1px solid var(--line);
    padding:28px 22px;
}

.brand{
    margin-bottom:28px;
}

.brand span{
    display:inline-block;
    padding:8px 12px;
    border-radius:999px;
    background:var(--primary-soft);
    color:var(--primary);
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.06em;
}

.brand h1{
    margin:14px 0 8px;
    font-size:28px;
    line-height:1.1;
}

.brand p{
    margin:0;
    color:var(--muted);
    line-height:1.7;
    font-size:14px;
}

.meta-card{
    padding:16px;
    border:1px solid var(--line);
    border-radius:18px;
    background:var(--panel);
    margin-bottom:14px;
}

.meta-card strong{
    display:block;
    margin-bottom:6px;
    font-size:13px;
    text-transform:uppercase;
    letter-spacing:0.06em;
    color:var(--muted);
}

.meta-card span{
    font-size:15px;
    font-weight:700;
}

.warning{
    margin-top:18px;
    padding:16px;
    border-radius:18px;
    background:#fff8e8;
    color:#9a6700;
    font-size:13px;
    line-height:1.7;
    border:1px solid #f5dfae;
}

.main{
    padding:28px;
}

.topbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-bottom:22px;
}

.topbar h2{
    margin:0 0 6px;
    font-size:32px;
}

.topbar p{
    margin:0;
    color:var(--muted);
    line-height:1.7;
}

.logout-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:48px;
    padding:0 18px;
    border-radius:14px;
    text-decoration:none;
    background:#fff;
    color:var(--danger);
    border:1px solid #ffd8d8;
    font-weight:700;
}

.ballot{
    background:var(--panel);
    border:1px solid var(--line);
    border-radius:28px;
    box-shadow:var(--shadow);
    overflow:hidden;
}

.ballot-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:22px 24px;
    border-bottom:1px solid var(--line);
    background:var(--panel-soft);
}

.ballot-head h3{
    margin:0 0 6px;
    font-size:24px;
}

.ballot-head p{
    margin:0;
    color:var(--muted);
    font-size:14px;
}

.status{
    display:inline-flex;
    align-items:center;
    padding:9px 12px;
    border-radius:999px;
    background:var(--success-soft);
    color:var(--success);
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.06em;
}

.table-wrap{
    padding:18px 24px 24px;
}

.vote-table{
    width:100%;
    border-collapse:collapse;
}

.vote-table th{
    text-align:left;
    padding:14px 16px;
    color:var(--muted);
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:0.06em;
    border-bottom:1px solid var(--line);
}

.vote-table td{
    padding:18px 16px;
    border-bottom:1px solid #ecf1f8;
    vertical-align:middle;
}

.vote-table tr:last-child td{
    border-bottom:none;
}

.candidate{
    display:flex;
    flex-direction:column;
    gap:5px;
}

.candidate strong{
    font-size:16px;
}

.candidate span{
    color:var(--muted);
    font-size:13px;
}

.department{
    display:inline-flex;
    align-items:center;
    padding:8px 12px;
    border-radius:999px;
    background:var(--primary-soft);
    color:var(--primary);
    font-size:12px;
    font-weight:700;
}

.radio-cell{
    text-align:center;
}

.radio-cell input[type="radio"]{
    width:20px;
    height:20px;
    accent-color:var(--primary);
}

.actions{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    margin-top:18px;
}

.helper{
    margin:0;
    color:var(--muted);
    font-size:14px;
    line-height:1.7;
}

.vote-btn{
    min-width:180px;
    height:52px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg, var(--primary), #2563eb);
    color:#fff;
    font:inherit;
    font-weight:700;
    cursor:pointer;
}

@media (max-width: 980px){
    .layout{ grid-template-columns:1fr; }
    .sidebar{ border-right:none; border-bottom:1px solid var(--line); }
}

@media (max-width: 720px){
    .main, .sidebar{ padding:20px 16px; }
    .topbar, .ballot-head, .actions{ flex-direction:column; align-items:flex-start; }
    .table-wrap{ padding:12px 16px 20px; }
    .vote-table, .vote-table thead, .vote-table tbody, .vote-table tr, .vote-table td{ display:block; width:100%; }
    .vote-table thead{ display:none; }
    .vote-table tr{
        border:1px solid var(--line);
        border-radius:18px;
        margin-bottom:14px;
        overflow:hidden;
        background:#fff;
    }
    .vote-table td{
        border-bottom:1px solid #ecf1f8;
    }
    .vote-table tr td:last-child{
        border-bottom:none;
    }
    .radio-cell{
        text-align:left;
    }
    .vote-btn{
        width:100%;
    }
}
</style>
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <span>Election Room</span>
            <h1>Cast Your Vote</h1>
            <p>Review the candidate list carefully and submit one final ballot for the current election.</p>
        </div>

        <div class="meta-card">
            <strong>Voter</strong>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>

        <div class="meta-card">
            <strong>Election Starts</strong>
            <span><?php echo date("d M Y, h:i A", $start_time); ?></span>
        </div>

        <div class="meta-card">
            <strong>Election Ends</strong>
            <span><?php echo date("d M Y, h:i A", $end_time); ?></span>
        </div>

        <div class="meta-card">
            <strong>Current Time</strong>
            <span><?php echo date("d M Y, h:i A", $current_time); ?></span>
        </div>

        <div class="warning">Once your vote is submitted, it cannot be edited or cast again.</div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div>
                <h2>Candidate Ballot</h2>
                <p>Select one candidate from the official election list below.</p>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <section class="ballot">
            <div class="ballot-head">
                <div>
                    <h3>Available Candidates</h3>
                    <p>Each candidate can be selected once as your final choice.</p>
                </div>
                <span class="status">Voting Open</span>
            </div>

            <div class="table-wrap">
                <form method="post">
                    <table class="vote-table">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Candidate</th>
                                <th>Department</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serial = 1;
                            while($row = mysqli_fetch_assoc($result)){
                            ?>
                            <tr>
                                <td><?php echo $serial++; ?></td>
                                <td>
                                    <div class="candidate">
                                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                        <span>Registered election candidate</span>
                                    </div>
                                </td>
                                <td><span class="department"><?php echo htmlspecialchars($row['Department']); ?></span></td>
                                <td class="radio-cell">
                                    <input type="radio" name="candidate" value="<?php echo $row['id']; ?>" required>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="actions">
                        <p class="helper">Please confirm your selection before submitting your ballot.</p>
                        <button type="submit" class="vote-btn">Submit Vote</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>

</body>
</html>
