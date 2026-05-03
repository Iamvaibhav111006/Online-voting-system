<?php
include("db.php");

$message = "";
$message_type = "";
$reset_link = "";

if(isset($_POST['verify'])){
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);

    $query = "SELECT * FROM users WHERE username='$username' AND phone='$phone'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $message = "Account verified. Continue to set a new password.";
        $message_type = "success";
        $reset_link = "reset_password.php?username=" . urlencode($username);
    } else {
        $message = "Invalid username or phone number.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#f4efe8;
            --panel:#fcfaf6;
            --text:#2f241c;
            --muted:#776558;
            --line:#e7dacb;
            --accent:#c2410c;
            --accent-dark:#9a3412;
            --success:#166534;
            --success-bg:#ecfdf3;
            --error:#b91c1c;
            --error-bg:#fef2f2;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Red Hat Display", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(194,65,12,0.12), transparent 24%),
                radial-gradient(circle at right 18%, rgba(234,179,8,0.12), transparent 22%),
                linear-gradient(180deg, #f8f3ec 0%, #efe6db 100%);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:28px 18px;
        }

        .wrap{
            width:min(1080px, 100%);
            display:grid;
            grid-template-columns:1.05fr 420px;
            gap:26px;
            align-items:center;
        }

        .story{
            padding:36px 18px 36px 0;
        }

        .story-tag{
            display:inline-flex;
            align-items:center;
            padding:10px 16px;
            border-radius:999px;
            background:#fff1e7;
            color:var(--accent);
            font-size:12px;
            font-weight:800;
            letter-spacing:0.08em;
            text-transform:uppercase;
        }

        .story h1{
            margin:22px 0 16px;
            max-width:620px;
            font-size:clamp(40px, 5vw, 70px);
            line-height:0.96;
        }

        .story p{
            margin:0;
            max-width:560px;
            color:var(--muted);
            font-size:18px;
            line-height:1.85;
        }

        .checklist{
            display:grid;
            gap:14px;
            margin-top:30px;
        }

        .item{
            padding:16px 18px;
            border-radius:20px;
            background:rgba(255,255,255,0.56);
            border:1px solid var(--line);
        }

        .item strong{
            display:block;
            margin-bottom:6px;
            font-size:16px;
        }

        .item span{
            color:var(--muted);
            font-size:14px;
            line-height:1.7;
        }

        .panel{
            background:linear-gradient(180deg, rgba(252,250,246,0.98) 0%, rgba(255,248,240,0.98) 100%);
            border:1px solid var(--line);
            border-radius:32px;
            padding:30px;
            box-shadow:0 28px 60px rgba(96, 70, 42, 0.12);
        }

        .panel h2{
            margin:0 0 10px;
            font-size:32px;
        }

        .panel-copy{
            margin:0 0 22px;
            color:var(--muted);
            line-height:1.8;
            font-size:15px;
        }

        .notice{
            margin-bottom:16px;
            padding:14px 16px;
            border-radius:16px;
            font-size:14px;
            font-weight:700;
        }

        .notice.success{
            background:var(--success-bg);
            color:var(--success);
            border:1px solid #bbf7d0;
        }

        .notice.error{
            background:var(--error-bg);
            color:var(--error);
            border:1px solid #fecaca;
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
            padding:0 16px;
            border-radius:16px;
            border:1px solid var(--line);
            background:#fffdf9;
            color:var(--text);
            font:inherit;
        }

        .field input:focus{
            outline:none;
            border-color:#fdba74;
            box-shadow:0 0 0 4px rgba(251,146,60,0.14);
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
            gap:14px;
            margin-top:18px;
            text-align:center;
        }

        .links a{
            color:var(--accent);
            text-decoration:none;
            font-weight:800;
        }

        @media (max-width: 980px){
            .wrap{ grid-template-columns:1fr; }
            .story{ padding-right:0; }
        }

        @media (max-width: 720px){
            .page{ padding:16px; }
            .panel{ padding:22px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="wrap">
            <div class="story">
                <span class="story-tag">Recovery Access</span>
                <h1>Recover your account without the usual mess.</h1>
                <p>Verify your voter account with the registered username and phone number, then continue to create a fresh password.</p>

                <div class="checklist">
                    <div class="item">
                        <strong>Username check</strong>
                        <span>Use the same username you entered during registration.</span>
                    </div>
                    <div class="item">
                        <strong>Phone verification</strong>
                        <span>The phone number must match the account details stored in the system.</span>
                    </div>
                    <div class="item">
                        <strong>Secure reset</strong>
                        <span>Once verified, you can move directly to the password update screen.</span>
                    </div>
                </div>
            </div>

            <aside class="panel">
                <h2>Forgot Password</h2>
                <p class="panel-copy">Enter your account details below to verify access.</p>

                <?php if($message != ""){ ?>
                    <div class="notice <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php } ?>

                <form method="POST">
                    <div class="field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                    </div>

                    <button type="submit" name="verify" class="submit">Verify Account</button>
                </form>

                <div class="links">
                    <?php if($reset_link != ""){ ?>
                        <a href="<?php echo htmlspecialchars($reset_link); ?>">Continue to Reset Password</a>
                    <?php } ?>
                    <a href="login.php">Back to Login</a>
                </div>
            </aside>
        </section>
    </main>
</body>
</html>
