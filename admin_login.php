<?php
session_start();
include("db.php");

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? AND role = 'admin'");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])){
            $_SESSION['admin'] = $username;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Wrong password";
        }
    } else {
        $error = "Admin not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#eef4f7;
            --panel:#ffffff;
            --soft:#f5f8fc;
            --line:#dbe4ee;
            --text:#1f2937;
            --muted:#6b7280;
            --accent:#ef4444;
            --accent-dark:#b91c1c;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Sora", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(239,68,68,0.10), transparent 24%),
                radial-gradient(circle at bottom right, rgba(59,130,246,0.12), transparent 22%),
                var(--bg);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:24px;
        }

        .wrap{
            width:min(980px, 100%);
            display:grid;
            grid-template-columns:1fr 400px;
            gap:22px;
        }

        .info{
            padding:40px;
            border-radius:30px;
            background:linear-gradient(180deg, rgba(255,255,255,0.96), rgba(247,250,252,0.96));
            border:1px solid var(--line);
            box-shadow:0 20px 40px rgba(15,23,42,0.08);
        }

        .info h1{
            margin:0 0 16px;
            font-size:clamp(36px, 5vw, 64px);
            line-height:1;
        }

        .info p{
            margin:0;
            max-width:520px;
            color:var(--muted);
            font-size:16px;
            line-height:1.9;
        }

        .admin-points{
            display:grid;
            gap:14px;
            margin-top:28px;
        }

        .admin-points div{
            padding:16px 18px;
            border-radius:18px;
            background:var(--soft);
            border:1px solid var(--line);
        }

        .admin-points strong{
            display:block;
            margin-bottom:6px;
            font-size:16px;
        }

        .admin-points span{
            color:var(--muted);
            font-size:14px;
            line-height:1.7;
        }

        .panel{
            background:var(--panel);
            border:1px solid var(--line);
            border-radius:30px;
            padding:30px;
            box-shadow:0 24px 48px rgba(15,23,42,0.10);
        }

        .panel h2{
            margin:0 0 8px;
            font-size:28px;
        }

        .panel p{
            margin:0 0 22px;
            color:var(--muted);
            line-height:1.8;
            font-size:14px;
        }

        .error{
            margin-bottom:16px;
            padding:14px 16px;
            border-radius:14px;
            background:#fef2f2;
            color:#b91c1c;
            border:1px solid #fecaca;
            font-size:14px;
            font-weight:700;
        }

        .field{
            margin-bottom:14px;
        }

        .field input{
            width:100%;
            height:54px;
            border-radius:16px;
            border:1px solid var(--line);
            padding:0 16px;
            font:inherit;
            color:var(--text);
            background:#fbfdff;
        }

        .field input:focus{
            outline:none;
            border-color:rgba(239,68,68,0.4);
            box-shadow:0 0 0 4px rgba(239,68,68,0.10);
        }

        .submit{
            width:100%;
            height:56px;
            border:none;
            border-radius:18px;
            background:linear-gradient(135deg, var(--accent), var(--accent-dark));
            color:#fff;
            font:inherit;
            font-weight:800;
            cursor:pointer;
            margin-top:6px;
        }

        .forgot{
            display:inline-block;
            margin-top:16px;
            color:#b91c1c;
            text-decoration:none;
            font-weight:700;
            font-size:14px;
        }

        @media (max-width: 900px){
            .wrap{ grid-template-columns:1fr; }
        }

        @media (max-width: 640px){
            .page{ padding:16px; }
            .info, .panel{ padding:24px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="wrap">
            <div class="info">
                <h1>Admin control starts here.</h1>
                <p>Access the election management console to configure candidates, monitor the process, and maintain system control.</p>

                <div class="admin-points">
                    <div>
                        <strong>Election management</strong>
                        <span>Control the start and end window for the live ballot.</span>
                    </div>
                    <div>
                        <strong>Candidate oversight</strong>
                        <span>Review the setup used for the active college election.</span>
                    </div>
                    <div>
                        <strong>Protected access</strong>
                        <span>Only admin accounts are allowed into this workspace.</span>
                    </div>
                </div>
            </div>

            <aside class="panel">
                <h2>Admin Login</h2>
                <p>Enter your admin credentials to continue to the dashboard.</p>

                <?php if($error != "") echo "<div class='error'>" . htmlspecialchars($error) . "</div>"; ?>

                <form method="POST">
                    <div class="field">
                        <input type="text" name="username" placeholder="Enter admin username" required>
                    </div>
                    <div class="field">
                        <input type="password" name="password" placeholder="Enter admin password" required>
                    </div>
                    <button type="submit" name="login" class="submit">Enter Dashboard</button>
                    <a href="admin_forgot_password.php" class="forgot">Forgot Password?</a>
                </form>
            </aside>
        </section>
    </main>
</body>
</html>
