<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

if(isset($_POST['set_election_time'])){

    $start_time = str_replace("T"," ", $_POST['start_time']);
    $end_time   = str_replace("T"," ", $_POST['end_time']);

    mysqli_query($conn,"UPDATE election_settings 
                        SET start_time='$start_time', end_time='$end_time'
                        WHERE id=1");

    header("Location: admin_dashboard.php");
    exit();
}

if(isset($_POST['add_candidate'])){
    $name = $_POST['candidate_name'];
    $department = $_POST['Department_name'];

    $query = "INSERT INTO candidates (name, Department)
              VALUES ('$name', '$department')";

    mysqli_query($conn, $query);
    header("Location: admin_dashboard.php");
    exit();
}

$candidate_total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM candidates"))['total'];
$voter_total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM users WHERE role='voter'"))['total'];
$votes_total = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM votes"))['total'];

$voters_query = mysqli_query($conn,"SELECT * FROM users WHERE role='voter'");
$election = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM election_settings LIMIT 1"));

$start_time = strtotime($election['start_time']);
$end_time = strtotime($election['end_time']);
$current_time = time();

if(isset($_GET['delete_id'])){

    if($current_time >= $start_time && $current_time <= $end_time){
        echo "<script>alert('Cannot delete candidate during election');</script>";
    }else{
        $delete_id = $_GET['delete_id'];

        mysqli_query($conn,"DELETE FROM candidates WHERE id='$delete_id'");

        header("Location: admin_dashboard.php");
        exit();
    }
}

$candidate_count_query = mysqli_query($conn,"SELECT COUNT(*) as total FROM candidates");
$candidate_count = mysqli_fetch_assoc($candidate_count_query);
$candidate_list = mysqli_query($conn,"SELECT * FROM candidates");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#f2f6fb;
            --panel:#ffffff;
            --panel-2:#f7faff;
            --panel-3:#f9fbfd;
            --line:#d9e3ef;
            --line-soft:#d3dcea;
            --text:#1e293b;
            --muted:#64748b;
            --green:#22c55e;
            --green-soft:rgba(34,197,94,0.12);
            --amber:#f59e0b;
            --amber-soft:rgba(245,158,11,0.12);
            --red:#ef4444;
            --red-soft:rgba(239,68,68,0.14);
            --blue:#38bdf8;
            --blue-soft:rgba(56,189,248,0.14);
            --shadow:0 28px 70px rgba(0,0,0,0.36);
        }

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Instrument Sans", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top right, rgba(56,189,248,0.12), transparent 22%),
                radial-gradient(circle at left 20%, rgba(34,197,94,0.10), transparent 20%),
                linear-gradient(180deg, #eef4fa 0%, #f6f9fc 100%);
        }

        .shell{
            max-width:1460px;
            margin:0 auto;
            padding:26px;
        }

        .hero{
            display:grid;
            grid-template-columns:minmax(0, 1.3fr) 320px;
            gap:22px;
            margin-bottom:22px;
        }

        .hero-main,
        .hero-side,
        .panel{
            border:1px solid var(--line);
            border-radius:28px;
            background:linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(247,250,255,0.98) 100%);
            box-shadow:var(--shadow);
        }

        .hero-main{
            padding:30px;
            position:relative;
            overflow:hidden;
        }

        .hero-main::before{
            content:"";
            position:absolute;
            top:-90px;
            right:-60px;
            width:220px;
            height:220px;
            border-radius:50%;
            background:rgba(56,189,248,0.10);
        }

        .eyebrow{
            display:inline-flex;
            align-items:center;
            padding:8px 12px;
            border-radius:999px;
            background:var(--blue-soft);
            color:var(--blue);
            font-size:12px;
            font-weight:700;
            letter-spacing:0.08em;
            text-transform:uppercase;
            font-family:"JetBrains Mono", monospace;
        }

        .hero-main h1{
            margin:18px 0 10px;
            font-size:clamp(34px, 5vw, 58px);
            line-height:0.98;
            letter-spacing:0;
        }

        .hero-main p{
            margin:0;
            max-width:720px;
            color:var(--muted);
            line-height:1.8;
            font-size:16px;
        }

        .hero-side{
            padding:24px;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            gap:18px;
        }

        .status-box{
            padding:16px;
            border-radius:20px;
            background:var(--panel-2);
            border:1px solid var(--line-soft);
        }

        .status-box strong{
            display:block;
            margin-bottom:8px;
            color:var(--muted);
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.08em;
            font-family:"JetBrains Mono", monospace;
        }

        .status-box span{
            font-size:18px;
            font-weight:700;
        }

        .top-actions{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .action-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:46px;
            padding:0 16px;
            border-radius:16px;
            text-decoration:none;
            font-weight:700;
            color:var(--text);
            border:1px solid var(--line-soft);
            background:var(--panel-2);
        }

        .action-link.danger{
            color:#b91c1c;
            border-color:rgba(239,68,68,0.16);
            background:#fef2f2;
        }

        .action-link.logout{
            color:#1d4ed8;
            border-color:rgba(56,189,248,0.18);
            background:#eff6ff;
        }

        .stats{
            display:grid;
            grid-template-columns:repeat(4, minmax(0, 1fr));
            gap:18px;
            margin-bottom:22px;
        }

        .stat{
            padding:22px;
            border-radius:24px;
            border:1px solid var(--line);
            background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow:var(--shadow);
        }

        .stat-label{
            display:block;
            margin-bottom:12px;
            color:var(--muted);
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.08em;
            font-family:"JetBrains Mono", monospace;
        }

        .stat-value{
            display:block;
            font-size:38px;
            font-weight:800;
        }

        .stat-note{
            display:block;
            margin-top:8px;
            color:var(--muted);
            font-size:14px;
        }

        .grid{
            display:grid;
            grid-template-columns:1.2fr 0.8fr;
            gap:22px;
        }

        .stack{
            display:grid;
            gap:22px;
        }

        .panel{
            padding:22px;
        }

        .panel-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:14px;
            margin-bottom:18px;
        }

        .panel-head h2,
        .panel-head h3{
            margin:0 0 6px;
            font-size:24px;
        }

        .panel-head p{
            margin:0;
            color:var(--muted);
            line-height:1.7;
            font-size:14px;
        }

        .table-wrap{
            overflow:auto;
            border:1px solid var(--line-soft);
            border-radius:20px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            min-width:640px;
        }

        th{
            padding:14px 16px;
            text-align:left;
            background:var(--panel-2);
            color:#475569;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.08em;
            font-family:"JetBrains Mono", monospace;
            border-bottom:1px solid var(--line-soft);
        }

        td{
            padding:16px;
            border-bottom:1px solid #e6edf5;
            color:var(--text);
        }

        tr:last-child td{
            border-bottom:none;
        }

        .status-pill{
            display:inline-flex;
            align-items:center;
            padding:8px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:0.08em;
            font-family:"JetBrains Mono", monospace;
        }

        .status-pill.voted{
            color:#86efac;
            background:var(--green-soft);
        }

        .status-pill.pending{
            color:#fcd34d;
            background:var(--amber-soft);
        }

        .form-grid{
            display:grid;
            gap:14px;
        }

        .field label{
            display:block;
            margin-bottom:8px;
            color:#475569;
            font-size:13px;
            font-weight:700;
        }

        .field input{
            width:100%;
            height:52px;
            padding:0 14px;
            border-radius:16px;
            border:1px solid var(--line-soft);
            background:var(--panel-3);
            color:var(--text);
            font:inherit;
        }

        .field input:focus{
            outline:none;
            border-color:rgba(56,189,248,0.42);
            box-shadow:0 0 0 4px rgba(56,189,248,0.10);
        }

        .submit-btn,
        .delete-link{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:48px;
            padding:0 16px;
            border:none;
            border-radius:16px;
            font:inherit;
            font-weight:700;
            cursor:pointer;
            text-decoration:none;
        }

        .submit-btn{
            background:linear-gradient(135deg, #38bdf8, #2563eb);
            color:#fff;
        }

        .delete-link{
            background:#fef2f2;
            color:#b91c1c;
            border:1px solid rgba(239,68,68,0.16);
        }

        .muted-note{
            padding:16px;
            border-radius:18px;
            background:#fffbeb;
            border:1px solid rgba(245,158,11,0.20);
            color:#b45309;
            line-height:1.7;
            font-size:14px;
        }

        @media (max-width: 1180px){
            .hero,
            .grid{
                grid-template-columns:1fr;
            }

            .stats{
                grid-template-columns:repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px){
            .shell{
                padding:16px;
            }

            .hero-main,
            .hero-side,
            .panel,
            .stat{
                padding:18px;
            }

            .stats{
                grid-template-columns:1fr;
            }

            .top-actions{
                flex-direction:column;
            }

            .action-link,
            .submit-btn{
                width:100%;
            }
        }
    </style>
</head>
<body>
<div class="shell">
    <section class="hero">
        <div class="hero-main">
            <span class="eyebrow">Admin Operations Console</span>
            <h1>Election control for <?php echo htmlspecialchars($_SESSION['admin']); ?></h1>
            <p>Monitor voter participation, manage candidates, schedule the election window, and keep the whole system under control from one screen.</p>
        </div>

        <aside class="hero-side">
            <div class="status-box">
                <strong>Election Window</strong>
                <span><?php echo date("d M Y, h:i A", $start_time); ?></span>
                <br><br>
                <span><?php echo date("d M Y, h:i A", $end_time); ?></span>
            </div>

            <div class="status-box">
                <strong>Current Time</strong>
                <span><?php echo date("d M Y, h:i A", $current_time); ?></span>
            </div>

            <div class="top-actions">
                <a href="admin_reset_votes.php" class="action-link danger">Reset All Votes</a>
                <a href="admin_logout.php" class="action-link logout">Logout</a>
            </div>
        </aside>
    </section>

    <section class="stats">
        <div class="stat">
            <span class="stat-label">Candidates</span>
            <span class="stat-value"><?php echo $candidate_total; ?></span>
            <span class="stat-note">Total registered candidates</span>
        </div>
        <div class="stat">
            <span class="stat-label">Voters</span>
            <span class="stat-value"><?php echo $voter_total; ?></span>
            <span class="stat-note">Eligible voter accounts</span>
        </div>
        <div class="stat">
            <span class="stat-label">Votes Cast</span>
            <span class="stat-value"><?php echo $votes_total; ?></span>
            <span class="stat-note">Submitted ballots so far</span>
        </div>
        <div class="stat">
            <span class="stat-label">Registry</span>
            <span class="stat-value"><?php echo $candidate_count['total']; ?></span>
            <span class="stat-note">Current candidate list size</span>
        </div>
    </section>

    <section class="grid">
        <div class="stack">
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Voter Status</h2>
                        <p>Track which voter accounts have already submitted a ballot.</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <tr>
                            <th>Voter Name</th>
                            <th>Voting Status</th>
                        </tr>
                        <?php
                        while($voter = mysqli_fetch_assoc($voters_query)){

                            $username = $voter['username'];

                            $check_vote = mysqli_query($conn,
                                "SELECT * FROM votes WHERE username='$username'"
                            );

                            if(mysqli_num_rows($check_vote) > 0){
                                $status = "Voted";
                                $status_class = "voted";
                            } else {
                                $status = "Not Voted";
                                $status_class = "pending";
                            }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($username); ?></td>
                            <td><span class="status-pill <?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Candidate Registry</h2>
                        <p>Review the active list and remove entries when the election window is closed.</p>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                        <?php while($row = mysqli_fetch_assoc($candidate_list)){ ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['Department']); ?></td>
                            <td>
                                <a href="admin_dashboard.php?delete_id=<?php echo $row['id']; ?>" class="delete-link">Delete</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>

        <div class="stack">
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Add Candidate</h3>
                        <p>Create a new candidate entry for the election registry.</p>
                    </div>
                </div>

                <?php
                if($current_time >= $start_time && $current_time <= $end_time){
                    echo "<div class='muted-note'>Candidate registration is closed during the election.</div>";
                }else{
                ?>
                <form method="POST" class="form-grid">
                    <div class="field">
                        <label for="candidate_name">Candidate Name</label>
                        <input type="text" id="candidate_name" name="candidate_name" placeholder="Enter candidate name" required>
                    </div>

                    <div class="field">
                        <label for="Department_name">Department</label>
                        <input type="text" id="Department_name" name="Department_name" placeholder="Enter department" required>
                    </div>

                    <button type="submit" name="add_candidate" class="submit-btn">Add Candidate</button>
                </form>
                <?php } ?>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>Set Election Time</h3>
                        <p>Configure the official voting window for the current election cycle.</p>
                    </div>
                </div>

                <form method="POST" class="form-grid">
                    <div class="field">
                        <label for="start_time">Start Time</label>
                        <input type="datetime-local" id="start_time" name="start_time" required>
                    </div>

                    <div class="field">
                        <label for="end_time">End Time</label>
                        <input type="datetime-local" id="end_time" name="end_time" required>
                    </div>

                    <button type="submit" name="set_election_time" class="submit-btn">Update Election Window</button>
                </form>
            </div>
        </div>
    </section>
</div>
</body>
</html>
