<?php
session_start();
include("db.php");

date_default_timezone_set("Asia/Kolkata");

$election_query = "SELECT * FROM election_control WHERE id=1";
$election_result = mysqli_query($conn, $election_query);
$election = mysqli_fetch_assoc($election_result);

$current_time = date("Y-m-d H:i:s");

if($current_time < $election['end_time']){
    echo "<h2 style='color:red;text-align:center;'>Results will be available after election ends</h2>";
    exit();
}

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

$query = "
SELECT candidates.id,
       candidates.name,
       candidates.Department,
       COUNT(votes.id) AS total_votes
FROM candidates
LEFT JOIN votes ON candidates.id = votes.candidate_id
GROUP BY candidates.id, candidates.name, candidates.Department
";
$result = mysqli_query($conn, $query);

$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM votes");
$total_row = mysqli_fetch_assoc($total_query);
$total_votes = $total_row['total'];

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

$winner_query = mysqli_query($conn,"
SELECT candidates.name, COUNT(votes.id) AS total_votes
FROM candidates
LEFT JOIN votes ON candidates.id = votes.candidate_id
GROUP BY candidates.id
HAVING total_votes = $max_votes
");

$rows = [];
$names = [];
$votes = [];
$serial = 1;

while($row = mysqli_fetch_assoc($result)){
    $row['serial'] = $serial++;
    $rows[] = $row;
    $names[] = $row['name'];
    $votes[] = (int)$row['total_votes'];
}

$winner_text = "";
if($total_votes == 0){
    $winner_text = "No votes have been cast yet.";
}else{
    $winner_count = mysqli_num_rows($winner_query);

    if($winner_count == 1){
        $winner = mysqli_fetch_assoc($winner_query);
        $winner_text = "Winner: " . $winner['name'] . " (" . $winner['total_votes'] . " votes)";
    }else{
        $winner_text = "Result: Tie between candidates";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Voting Results</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#0e1220;
            --bg-2:#151b2d;
            --panel:#13192a;
            --panel-soft:#1b2338;
            --line:rgba(255,255,255,0.10);
            --gold:#f4c95d;
            --gold-soft:rgba(244,201,93,0.12);
            --blue:#60a5fa;
            --pink:#f472b6;
            --mint:#34d399;
            --text:#f8f5ee;
            --muted:#c4c0b6;
            --shadow:0 30px 80px rgba(0,0,0,0.38);
        }

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Public Sans", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top center, rgba(244,201,93,0.10), transparent 24%),
                radial-gradient(circle at left 20%, rgba(96,165,250,0.10), transparent 20%),
                radial-gradient(circle at right 28%, rgba(244,114,182,0.10), transparent 18%),
                linear-gradient(180deg, #090d17 0%, #0e1220 50%, #141a2d 100%);
        }

        .page{
            max-width:1400px;
            margin:0 auto;
            padding:28px;
        }

        .hero{
            position:relative;
            overflow:hidden;
            padding:36px;
            border:1px solid var(--line);
            border-radius:34px;
            background:
                linear-gradient(135deg, rgba(244,201,93,0.08), rgba(255,255,255,0.02)),
                linear-gradient(180deg, rgba(19,25,42,0.98), rgba(12,16,28,0.98));
            box-shadow:var(--shadow);
            margin-bottom:24px;
        }

        .hero::before,
        .hero::after{
            content:"";
            position:absolute;
            border-radius:50%;
            pointer-events:none;
        }

        .hero::before{
            width:260px;
            height:260px;
            top:-120px;
            right:-80px;
            background:rgba(244,201,93,0.10);
        }

        .hero::after{
            width:180px;
            height:180px;
            bottom:-80px;
            left:-40px;
            background:rgba(96,165,250,0.08);
        }

        .hero-top{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:18px;
        }

        .eyebrow{
            display:inline-flex;
            align-items:center;
            padding:9px 14px;
            border-radius:999px;
            background:var(--gold-soft);
            color:var(--gold);
            border:1px solid rgba(244,201,93,0.18);
            font-size:12px;
            font-weight:700;
            letter-spacing:0.08em;
            text-transform:uppercase;
        }

        .welcome{
            margin:18px 0 8px;
            color:#f5e7bf;
            font-size:20px;
            font-weight:600;
        }

        .title{
            margin:0;
            font-family:"Cinzel", serif;
            font-size:clamp(42px, 6vw, 82px);
            line-height:0.96;
            letter-spacing:0.02em;
            text-transform:uppercase;
        }

        .hero-copy{
            margin:18px 0 0;
            max-width:760px;
            color:var(--muted);
            font-size:16px;
            line-height:1.9;
        }

        .logout-btn{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:48px;
            padding:0 18px;
            border-radius:16px;
            text-decoration:none;
            font-weight:700;
            color:#fff;
            background:rgba(255,255,255,0.06);
            border:1px solid var(--line);
            backdrop-filter:blur(8px);
        }

        .stats{
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:18px;
            margin-top:26px;
        }

        .stat{
            padding:20px 22px;
            border-radius:24px;
            background:rgba(255,255,255,0.04);
            border:1px solid var(--line);
        }

        .stat-label{
            display:block;
            margin-bottom:10px;
            color:#d6d2c7;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.08em;
        }

        .stat-value{
            display:block;
            font-family:"Cinzel", serif;
            font-size:34px;
            color:var(--gold);
        }

        .grid{
            display:grid;
            grid-template-columns:1.1fr 0.9fr;
            gap:24px;
        }

        .panel{
            border:1px solid var(--line);
            border-radius:30px;
            background:linear-gradient(180deg, rgba(19,25,42,0.98), rgba(13,18,31,0.98));
            box-shadow:var(--shadow);
            overflow:hidden;
        }

        .panel-head{
            padding:24px 26px 18px;
            border-bottom:1px solid var(--line);
        }

        .panel-head h2{
            margin:0 0 8px;
            font-family:"Cinzel", serif;
            font-size:28px;
            letter-spacing:0.02em;
        }

        .panel-head p{
            margin:0;
            color:var(--muted);
            line-height:1.8;
            font-size:14px;
        }

        .table-wrap{
            overflow:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
            min-width:640px;
        }

        th{
            padding:16px 18px;
            text-align:left;
            color:#f3e6bd;
            background:rgba(244,201,93,0.06);
            border-bottom:1px solid var(--line);
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.08em;
        }

        td{
            padding:18px;
            border-bottom:1px solid rgba(255,255,255,0.06);
        }

        tr:last-child td{
            border-bottom:none;
        }

        tr:nth-child(even){
            background:rgba(255,255,255,0.02);
        }

        .candidate-cell strong{
            display:block;
            margin-bottom:6px;
            font-size:17px;
        }

        .candidate-cell span{
            color:var(--muted);
            font-size:13px;
        }

        .vote-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-width:48px;
            padding:8px 12px;
            border-radius:999px;
            background:rgba(52,211,153,0.12);
            color:#9ff5cf;
            font-weight:700;
        }

        .winner-board{
            padding:24px 26px 28px;
            display:grid;
            gap:18px;
        }

        .winner-card{
            padding:22px;
            border-radius:26px;
            background:
                linear-gradient(135deg, rgba(244,201,93,0.14), rgba(255,255,255,0.04));
            border:1px solid rgba(244,201,93,0.22);
            text-align:center;
        }

        .winner-card h3{
            margin:0 0 10px;
            font-family:"Cinzel", serif;
            font-size:24px;
            color:var(--gold);
        }

        .winner-card p{
            margin:0;
            color:#fff3ce;
            font-size:18px;
            line-height:1.7;
        }

        .chart-wrap{
            padding:16px 10px 8px;
            max-width:430px;
            margin:0 auto;
        }

        .back-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:48px;
            padding:0 18px;
            border-radius:16px;
            text-decoration:none;
            color:#dbeafe;
            background:rgba(96,165,250,0.10);
            border:1px solid rgba(96,165,250,0.18);
            font-weight:700;
            margin-top:4px;
        }

        @media (max-width: 1080px){
            .grid{
                grid-template-columns:1fr;
            }

            .stats{
                grid-template-columns:1fr;
            }
        }

        @media (max-width: 720px){
            .page{
                padding:16px;
            }

            .hero{
                padding:24px;
            }

            .hero-top{
                flex-direction:column;
                align-items:flex-start;
            }

            .panel-head,
            .winner-board{
                padding-left:18px;
                padding-right:18px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="hero">
            <div class="hero-top">
                <div>
                    <span class="eyebrow">Official Result Board</span>
                    <p class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <h1 class="title">Voting Results</h1>
                    <p class="hero-copy">The election window has closed. This board shows the final candidate standings, total ballots recorded, and the declared result for the current session.</p>
                </div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>

            <div class="stats">
                <div class="stat">
                    <span class="stat-label">Total Votes Cast</span>
                    <span class="stat-value"><?php echo $total_votes; ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Candidates</span>
                    <span class="stat-value"><?php echo count($rows); ?></span>
                </div>
                <div class="stat">
                    <span class="stat-label">Result Status</span>
                    <span class="stat-value"><?php echo $total_votes > 0 ? "Final" : "Empty"; ?></span>
                </div>
            </div>
        </section>

        <section class="grid">
            <div class="panel">
                <div class="panel-head">
                    <h2>Final Standings</h2>
                    <p>Every candidate is listed below with their department and total recorded votes.</p>
                </div>

                <div class="table-wrap">
                    <table>
                        <tr>
                            <th>Sr. No</th>
                            <th>Candidate</th>
                            <th>Department</th>
                            <th>Total Votes</th>
                        </tr>
                        <?php foreach($rows as $row){ ?>
                        <tr>
                            <td><?php echo $row['serial']; ?></td>
                            <td class="candidate-cell">
                                <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                                <span>Election candidate</span>
                            </td>
                            <td><?php echo htmlspecialchars($row['Department']); ?></td>
                            <td><span class="vote-badge"><?php echo $row['total_votes']; ?></span></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <h2>Declared Result</h2>
                    <p>A summary view of the winner announcement and vote distribution.</p>
                </div>

                <div class="winner-board">
                    <div class="winner-card">
                        <h3>Election Outcome</h3>
                        <p><?php echo htmlspecialchars($winner_text); ?></p>
                    </div>

                    <div class="chart-wrap">
                        <canvas id="voteChart"></canvas>
                    </div>

                    <a class="back-link" href="vote.php">Back to Voting</a>
                </div>
            </div>
        </section>
    </main>

    <script>
    const ctx = document.getElementById('voteChart');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($names); ?>,
            datasets: [{
                label: 'Votes',
                data: <?php echo json_encode($votes); ?>,
                backgroundColor: ['#f4c95d', '#60a5fa', '#f472b6', '#34d399', '#c084fc', '#fb7185'],
                borderColor: '#0f1422',
                borderWidth: 4,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: {
                        color: '#f8f5ee',
                        font: {
                            family: 'Public Sans'
                        }
                    }
                }
            }
        }
    });
    </script>
</body>
</html>
