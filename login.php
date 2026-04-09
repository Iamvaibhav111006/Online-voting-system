<?php
error_reporting(E_ALL & ~E_NOTICE); // ignore notices
session_start();
include("db.php");

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user by username
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);

        // Verify hashed password
        if(password_verify($password, $row['password'])){
            $_SESSION['username'] = $username;
            header("Location: vote.php");
            exit();
        } else {
            echo "Invalid username or password";
        }

    } else {
        echo "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Online Voting System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            width: 350px;
            text-align: center;
            color: #fff;
        }
        .login-container h2 {
            margin-bottom: 25px;
            text-shadow: 2px 2px 6px #000;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 8px;
            border: none;
        }
        button {
            width: 95%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            background: #ff5f6d;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #ffc371;
            color: #000;
        }
        a {
            color: #fff;
            text-decoration: underline;
        }
        a:hover {
            color: #ffc371;
        }
        p {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
    