<?php
include("db.php");

if(!isset($_GET['username'])){
    echo "Invalid Access!";
    exit();
}

$username = $_GET['username'];
$message = "";
$message_type = "";

if(isset($_POST['reset'])){
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update_query = "UPDATE users SET password='$hashed_password' WHERE username='$username'";

    if(mysqli_query($conn, $update_query)){
        $message = "Password updated successfully. You can sign in now.";
        $message_type = "success";
    } else {
        $message = "Error updating password.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#1d140f;
            --panel:#291a13;
            --panel-soft:#332118;
            --text:#fff5ee;
            --muted:#d9b8a0;
            --line:#52352b;
            --accent:#fb7185;
            --accent-dark:#e11d48;
            --success:#bbf7d0;
            --success-bg:rgba(34,197,94,0.14);
            --error:#fecaca;
            --error-bg:rgba(239,68,68,0.14);
        }

        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Red Hat Display", sans-serif;
            color:var(--text);
            background:
                radial-gradient(circle at top left, rgba(251,113,133,0.18), transparent 24%),
                radial-gradient(circle at right 20%, rgba(249,115,22,0.14), transparent 20%),
                linear-gradient(180deg, #120c08 0%, #1d140f 100%);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:28px 18px;
        }

        .card{
            width:min(540px, 100%);
            padding:32px;
            border-radius:32px;
            background:linear-gradient(180deg, rgba(41,26,19,0.98) 0%, rgba(29,20,15,0.98) 100%);
            border:1px solid var(--line);
            box-shadow:0 30px 70px rgba(0,0,0,0.28);
        }

        .card h1{
            margin:0 0 10px;
            font-size:38px;
            line-height:1;
        }

        .card p{
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
            color:var(--success);
            background:var(--success-bg);
            border:1px solid rgba(34,197,94,0.22);
        }

        .notice.error{
            color:var(--error);
            background:var(--error-bg);
            border:1px solid rgba(239,68,68,0.22);
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
            height:56px;
            padding:0 16px;
            border-radius:18px;
            border:1px solid var(--line);
            background:var(--panel-soft);
            color:var(--text);
            font:inherit;
        }

        .field input:focus{
            outline:none;
            border-color:rgba(251,113,133,0.42);
            box-shadow:0 0 0 4px rgba(251,113,133,0.12);
        }

        .submit{
            width:100%;
            height:58px;
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
            color:#fda4af;
            text-decoration:none;
            font-weight:800;
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card">
            <h1>Set New Password</h1>
            <p>Create a new password for <strong><?php echo htmlspecialchars($username); ?></strong> and update your account access.</p>

            <?php if($message != ""){ ?>
                <div class="notice <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                </div>

                <button type="submit" name="reset" class="submit">Update Password</button>
            </form>

            <div class="links">
                <a href="login.php">Back to Login</a>
            </div>
        </section>
    </main>
</body>
</html>
