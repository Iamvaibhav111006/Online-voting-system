<!DOCTYPE html>
<html>
<head>
    <title>Online Voting System</title>

    <style>
        body{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color:white;
            text-align:center;
        }

        .hero{
            padding-top:120px;
        }

        h1{
            font-size:50px;
            margin-bottom:10px;
        }

        h2{
            font-weight:normal;
            margin-bottom:60px;
        }

        .login-section{
            background:white;
            color:black;
            padding:40px 20px;
            border-radius:15px 15px 0 0;
            margin-top:80px;
        }

        .btn{
            display:inline-block;
            text-decoration:none;
            padding:15px 40px;
            margin:20px;
            border-radius:8px;
            font-size:18px;
            font-weight:bold;
            color:white;
        }

        .admin{
            background:#ff4b5c;
        }

        .voter{
            background:#1abc9c;
        }

        .btn:hover{
            opacity:0.85;
        }
        .credit{
    margin-top:40px;
    font-style:italic;
    color:#555;
        }
    </style>
</head>

<body>

<div class="hero">
    <h1>Welcome to College Online Voting System</h1>
    <h2>A Secure and Transparent Digital College Election Platform</h2>
</div>

<div class="login-section">
    <h3>Select Your Portal</h3>
    <a href="admin_login.php" class="btn admin">Admin Login</a>
    <a href="login.php" class="btn voter">Voter Login</a>
    <p class="credit">Made by Vaibhav, 24-cse-231</p>
</div>

</body>
</html>