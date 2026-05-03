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

    $update_query = "UPDATE users SET password='$hashed_password' WHERE username='$username' AND role='admin'";

    if(mysqli_query($conn, $update_query)){
        $message = "Admin password updated successfully.";
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
    <title>Admin Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg:#eef7fb;
            --panel:#ffffff;
            --panel-soft:#f6fbff;
            --text:#142633;
            --muted:#607685;
            --line:#d8e7f0;
            --accent:#0369a1;
            --accent-dark:#075985;
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
                radial-gradient(circle at top left, rgba(3,105,161,0.14), transparent 24%),
                radial-gradient(circle at right 18%, rgba(6,182,212,0.10), transparent 18%),
                linear-gradient(180deg, #f5fbff 0%, #eaf4f9 100%);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:24px;
        }

        .card{
            width:min(560px, 100%);
            padding:32px;
            border-radius:32px;
            background:linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(246,251,255,0.98) 100%);
            border:1px solid var(--line);
            box-shadow:0 28px 60px rgba(20, 78, 105, 0.12);
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
            border-color:#38bdf8;
            box-shadow:0 0 0 4px rgba(56,189,248,0.14);
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
            color:var(--accent);
            text-decoration:none;
            font-weight:800;
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="card">
            <h1>Set New Admin Password</h1>
            <p>Create a new password for <strong><?php echo htmlspecialchars($username); ?></strong> to restore administrator dashboard access.</p>

            <?php if($message != ""){ ?>
                <div class="notice <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php } ?>

            <form method="POST">
                <div class="field">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new admin password" required>
                </div>

                <button type="submit" name="reset" class="submit">Update Admin Password</button>
            </form>

            <div class="links">
                <a href="admin_login.php">Back to Login</a>
            </div>
        </section>
    </main>
</body>
</html>
