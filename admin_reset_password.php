<?php
include("db.php");

if(!isset($_GET['username'])){
    echo "Invalid Access!";
    exit();
}

$username = $_GET['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reset Password</title>
    <style>
body {
    background: linear-gradient(135deg, #667eea, #764ba2);
    font-family: 'Segoe UI', sans-serif;
}

.reset-card {
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

.reset-card h2 {
    margin-bottom: 20px;
}

.reset-card input[type="password"] {
    width: 80%;
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: none;
}

.reset-card input[type="submit"] {
    padding: 12px 25px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.reset-card input[type="submit"]:hover {
    opacity: 0.85;
}

.reset-card a {
    color: white;
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="reset-card">
    <h2>Set New Admin Password</h2>

    <form method="POST">
        <input type="password" name="new_password" placeholder="Enter new password" required><br><br>
        <input type="submit" name="reset" value="Update Password">
    </form>

    <br>
    <a href="admin_login.php">Back to Login</a>

    <?php
    if(isset($_POST['reset'])){
        $new_password = $_POST['new_password'];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update_query = "UPDATE users SET password='$hashed_password' WHERE username='$username' AND role='admin'";

        if(mysqli_query($conn, $update_query)){
            echo "<br><br><span style='color:lightgreen;'>Password Updated Successfully!</span>";
        } else {
            echo "<br><br><span style='color:red;'>Error updating password.</span>";
        }
    }
    ?>
</div>
</body>
</html>