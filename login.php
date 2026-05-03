<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include("db.php");

$error_message = "";

if(isset($_POST['username'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Using prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);

        if(password_verify($password, $row['password'])){
            $_SESSION['username'] = $username;
            header("Location: face_login.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#eef7f5;
            --text:#102a2b;
            --muted:#547172;
            --panel:#ffffff;
            --line:#d5e8e4;
            --accent:#0f766e;
            --accent-dark:#115e59;
            --danger:#b91c1c;
            --danger-bg:#fef2f2;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Manrope", sans-serif;
            color:var(--text);
            background:
                linear-gradient(120deg, rgba(15,118,110,0.12), transparent 30%),
                linear-gradient(300deg, rgba(14,165,233,0.10), transparent 30%),
                var(--bg);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:24px;
        }

        .shell{
            width:min(1060px, 100%);
            display:grid;
            grid-template-columns:1fr 430px;
            gap:24px;
            align-items:stretch;
        }

        .story{
            border-radius:34px;
            padding:42px;
            background:
                radial-gradient(circle at top right, rgba(15,118,110,0.16), transparent 28%),
                #f8fffd;
            border:1px solid #d8ebe7;
        }

        .story h1{
            margin:0 0 16px;
            font-size:clamp(38px, 5vw, 68px);
            line-height:1;
        }

        .story p{
            margin:0;
            max-width:520px;
            color:var(--muted);
            font-size:18px;
            line-height:1.8;
        }

        .story-grid{
            display:grid;
            grid-template-columns:repeat(2, minmax(0, 1fr));
            gap:16px;
            margin-top:30px;
        }

        .story-card{
            padding:18px;
            border-radius:22px;
            background:#fff;
            border:1px solid #dcebe7;
        }

        .story-card strong{
            display:block;
            margin-bottom:8px;
            font-size:18px;
        }

        .story-card span{
            color:var(--muted);
            font-size:14px;
            line-height:1.7;
        }

        .panel{
            background:var(--panel);
            border-radius:30px;
            padding:30px;
            box-shadow:0 26px 50px rgba(15, 59, 57, 0.10);
            border:1px solid #dbece8;
        }

        .panel h2{
            margin:0 0 8px;
            font-size:32px;
        }

        .panel-copy{
            margin:0 0 22px;
            color:var(--muted);
            line-height:1.7;
        }

        .error-box{
            margin-bottom:16px;
            padding:14px 16px;
            border-radius:16px;
            color:var(--danger);
            background:var(--danger-bg);
            border:1px solid #fecaca;
            font-size:14px;
            font-weight:700;
        }

        .field{
            margin-bottom:16px;
        }

        .field label{
            display:block;
            margin-bottom:8px;
            font-size:14px;
            font-weight:700;
        }

        .field input{
            width:100%;
            height:54px;
            border:1px solid var(--line);
            border-radius:16px;
            padding:0 16px;
            font:inherit;
            background:#fbfffe;
        }

        .field input:focus{
            outline:none;
            border-color:#5eead4;
            box-shadow:0 0 0 4px rgba(15,118,110,0.10);
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
        }

        .links{
            display:grid;
            gap:12px;
            text-align:center;
            margin-top:18px;
        }

        .links a{
            color:var(--accent);
            text-decoration:none;
            font-weight:700;
        }

        .secondary{
            color:var(--muted);
            font-size:14px;
        }

        @media (max-width: 980px){
            .shell{ grid-template-columns:1fr; }
        }

        @media (max-width: 720px){
            .page{ padding:16px; }
            .story, .panel{ padding:24px; }
            .story-grid{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="shell">
            <div class="story">
                <h1>Welcome back to the voter portal.</h1>
                <p>Sign in with your registered credentials to continue into face verification and access the live election flow.</p>

                <div class="story-grid">
                    <div class="story-card">
                        <strong>Secure sign-in</strong>
                        <span>Only registered accounts can move forward to the next verification stage.</span>
                    </div>
                    <div class="story-card">
                        <strong>Simple next step</strong>
                        <span>Login takes you directly into the identity check required before voting.</span>
                    </div>
                </div>
            </div>

            <aside class="panel">
                <h2>Voter Login</h2>
                <p class="panel-copy">Enter your username and password to continue.</p>

                <?php if($error_message != ""){ ?>
                    <div class="error-box"><?php echo htmlspecialchars($error_message); ?></div>
                <?php } ?>

                <form method="post">
                    <div class="field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" name="login" class="submit">Continue</button>
                </form>

                <div class="links">
                    <a href="forgot_password.php">Forgot Password?</a>
                    <div class="secondary">Need an account? <a href="register.php">Register</a></div>
                </div>
            </aside>
        </section>
    </main>
</body>
</html>
