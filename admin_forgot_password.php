<?php
include("db.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Forgot Password</title>
    <style>
    body {
        background: linear-gradient(135deg, #f6d365, #fda085);
        font-family: 'Segoe UI', sans-serif;
    }

    .forgot-card {
        width: 350px;
        margin: 100px auto;
        padding: 30px;
        border-radius: 15px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        text-align: center;
        color: white;
    }

    .forgot-card h2 {
        margin-bottom: 20px;
    }

    .forgot-card input[type="text"] {
        width: 80%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 10px;
        border: none;
    }

    .forgot-card input[type="submit"] {
        padding: 12px 25px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(45deg, #ff416c, #ff4b2b);
        color: white;
        font-weight: bold;
        cursor: pointer;
    }

    .forgot-card input[type="submit"]:hover {
        opacity: 0.85;
    }

    .forgot-card a {
        color: white;
        text-decoration: underline;
    }
    </style>
</head>
<body>

<div class="forgot-card">
    <h2>Admin Forgot Password</h2>

    <form method="POST">
        Username: <input type="text" name="username" placeholder="Enter your username" required><br><br>
        Phone: <input type="text" name="phone" placeholder="Enter your phone number" required><br><br>
        <input type="submit" name="verify" value="Verify">
    </form>

    <br>
    <a href="admin_login.php">Back to Login</a>

    <?php
    if(isset($_POST['verify'])){
        $username = $_POST['username'];
        $phone = $_POST['phone'];

        $query = "SELECT * FROM users WHERE username='$username' AND phone='$phone' AND role='admin'";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) == 1){
            echo "<br><a href='admin_reset_password.php?username=$username'>Click here to reset password</a>";
        } else {
            echo "<br>Invalid Username or Phone!";
        }
    }
    ?>
</div>

</body>
</html>