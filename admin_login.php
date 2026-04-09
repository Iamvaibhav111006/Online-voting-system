<?php
session_start();
include("db.php");

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check admin from database
    $query = "SELECT * FROM users WHERE username='$username' AND role='admin'";
    $result = mysqli_query($conn,$query);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
    if($password == $row['password']){
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
<html>
<head>
    <title>Admin Login</title>
    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background: linear-gradient(to right, #141e30, #243b55);
        }

        .login-box{
            background:white;
            padding:40px;
            width:350px;
            border-radius:10px;
            box-shadow:0 10px 25px rgba(0,0,0,0.4);
            text-align:center;
        }

        h2{
            margin-bottom:20px;
            color:#243b55;
        }

        input{
            width:100%;
            padding:10px;
            margin:10px 0;
            border-radius:5px;
            border:1px solid #ccc;
        }

        button{
            width:100%;
            padding:10px;
            background:#243b55;
            color:white;
            border:none;
            border-radius:5px;
            cursor:pointer;
            font-size:16px;
        }

        button:hover{
            background:#141e30;
        }

        .error{
            color:red;
            margin-bottom:10px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>