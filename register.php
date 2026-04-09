<?php
include("db.php");

if(isset($_POST['register'])){

    $name = trim($_POST['name']);
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // AGE CHECK
    $age = date_diff(date_create($dob), date_create('today'))->y;

    if($age < 18){
        echo "You must be 18+ to register";
    } else {

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users(name,dob,phone,address,username,password)
                  VALUES('$name','$dob','$phone','$address','$username','$hashed_password')";

        if(mysqli_query($conn,$query)){
            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Voter Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #11998e, #38ef7d);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
            width: 400px;
            text-align: center;
            color: #fff;
        }
        h2 {
            margin-bottom: 25px;
            text-shadow: 2px 2px 6px #000;
        }
        input[type="text"], input[type="password"], input[type="date"] {
            width: 90%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
        }
        button {
            width: 95%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 8px;
            background: #27ae60;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #2ecc71;
            color: #000;
        }
        a {
            color: #fff;
            text-decoration: underline;
        }
        a:hover {
            color: #000;
        }
        p {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="register-container">
    <h2>Voter Registration</h2>
    <form method="POST" action="register.php">
        Name:<br>
        <input type="text" name="name" required><br>
        Date of Birth:<br>
        <input type="date" name="dob" required><br>
        Phone:<br>
        <input type="text" name="phone" required><br>
        Address:<br>
        <input type="text" name="address" required><br>
        Username:<br>
        <input type="text" name="username" required><br>
        Password:<br>
        <input type="password" name="password" required><br>
        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>