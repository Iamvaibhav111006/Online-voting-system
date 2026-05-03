<?php
include("db.php");

$message = "";
$message_type = "";
$reset_link = "";

if(isset($_POST['verify'])){
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);

    $query = "SELECT * FROM users WHERE username='$username' AND phone='$phone' AND role='admin'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $message = "Admin account verified. Continue to reset the password.";
        $message_type = "success";
        $reset_link = "admin_reset_password.php?username=" . urlencode($username);
    } else {
        $message = "Invalid admin username or phone number.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#f5f8ee;
            --panel:#fbfdf7;
            --text:#22311f;
            --muted:#6d7b6b;
            --line:#dce6d7;
            --accent:#3f6212;
            --accent-dark:#365314;
            --success:#166534;
            --success-bg:#ecfdf3;
            --error:#b91c1c;
            --error-bg:#fef2f2;
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Urbanist", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(101,163,13,0.14), transparent 24%),
                radial-gradient(circle at right 20%, rgba(14,165,233,0.10), transparent 18%),
                linear-gradient(180deg, #f8fbf3 0%, #edf3e4 100%);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:24px;
        }

        .shell{
            width:min(1100px, 100%);
            display:grid;
            grid-template-columns:1fr 420px;
            gap:26px;
            align-items:center;
        }

        .intro{
            padding-right:12px;
        }

        .tag{
            display:inline-flex;
            align-items:center;
            padding:10px 16px;
            border-radius:999px;
            background:#eef6df;
            color:var(--accent);
            font-size:12px;
            font-weight:800;
            letter-spacing:0.08em;
            text-transform:uppercase;
        }

        .intro h1{
            margin:22px 0 16px;
            max-width:650px;
            font-size:clamp(42px, 5vw, 72px);
            line-height:0.94;
        }

        .intro p{
            margin:0;
            max-width:560px;
            color:var(--muted);
            font-size:18px;
            line-height:1.85;
        }

        .points{
            display:grid;
            gap:14px;
            margin-top:30px;
        }

        .point{
            padding:16px 18px;
            border-radius:20px;
            background:rgba(255,255,255,0.60);
            border:1px solid var(--line);
        }

        .point strong{
            display:block;
            margin-bottom:6px;
            font-size:16px;
        }

        .point span{
            color:var(--muted);
            font-size:14px;
            line-height:1.7;
        }

        .panel{
            background:linear-gradient(180deg, rgba(251,253,247,0.98) 0%, rgba(245,249,239,0.98) 100%);
            border:1px solid var(--line);
            border-radius:30px;
            padding:30px;
            box-shadow:0 28px 50px rgba(74, 98, 43, 0.10);
        }

        .panel h2{
            margin:0 0 10px;
            font-size:32px;
        }

        .panel p{
            margin:0 0 22px;
            color:var(--muted);
            font-size:15px;
            line-height:1.8;
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
            background:#ffffff;
            color:var(--text);
            font:inherit;
        }

        .field input:focus{
            outline:none;
            border-color:#84cc16;
            box-shadow:0 0 0 4px rgba(132,204,22,0.14);
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
            .shell{ grid-template-columns:1fr; }
            .intro{ padding-right:0; }
        }

        @media (max-width: 720px){
            .page{ padding:16px; }
            .panel{ padding:22px; }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="shell">
            <div class="intro">
                <span class="tag">Admin Recovery</span>
                <h1>Recover admin access with a cleaner, safer flow.</h1>
                <p>Verify the administrator account details first, then continue to update the password for dashboard access.</p>

                <div class="points">
                    <div class="point">
                        <strong>Restricted to admin accounts</strong>
                        <span>This recovery flow checks only records with admin role access.</span>
                    </div>
                    <div class="point">
                        <strong>Phone-based verification</strong>
                        <span>The phone number must match the administrator account in the database.</span>
                    </div>
                    <div class="point">
                        <strong>Direct reset continuation</strong>
                        <span>Once verified, the system gives you the secure password reset step immediately.</span>
                    </div>
                </div>
            </div>

            <aside class="panel">
                <h2>Admin Forgot Password</h2>
                <p>Verify your admin account details below to continue.</p>

                <?php if($message != ""){ ?>
                    <div class="notice <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php } ?>

                <form method="POST">
                    <div class="field">
                        <label for="username">Admin Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter admin username" required>
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter phone number" required>
                    </div>

                    <button type="submit" name="verify" class="submit">Verify Admin Account</button>
                </form>

                <div class="links">
                    <?php if($reset_link != ""){ ?>
                        <a href="<?php echo htmlspecialchars($reset_link); ?>">Continue to Reset Password</a>
                    <?php } ?>
                    <a href="admin_login.php">Back to Login</a>
                </div>
            </aside>
        </section>
    </main>
</body>
</html>
