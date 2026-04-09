<?php
include("db.php");
if(isset($_GET['delete_id'])){
    $delete_id = $_GET['delete_id'];

    mysqli_query($conn,
        "DELETE FROM candidates WHERE id='$delete_id'"
    );

    header("Location: admin_dashboard.php");
    exit();
}
?>
<?php
session_start();
include("db.php");

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            color:white;
            text-align:center;
        }

        .container{
            margin-top:50px;
        }

        .card{
            background:white;
            color:black;
            width:60%;
            margin:20px auto;
            padding:20px;
            border-radius:10px;
            box-shadow:0 5px 15px rgba(0,0,0,0.3);
        }

        input{
            padding:8px;
            margin:5px;
            width:60%;
        }

        button{
            padding:8px 15px;
            background:#2a5298;
            color:white;
            border:none;
            border-radius:5px;
            cursor:pointer;
        }

        button:hover{
            background:#1e3c72;
        }

        .danger{
            background:#f44336;
        }

        .logout{
            background:black;
            margin-top:20px;
        }
        .danger {
    background: #ff4b2b;
    color: white;
    padding: 8px 15px;
    border-radius: 8px;
    text-decoration: none;
}

        a{
            text-decoration:none;
            color:white;
        }

        table{
            width:100%;
            margin-top:10px;
        }

        th, td{
            padding:8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome Admin, <?php echo $_SESSION['admin']; ?> 👋</h1>

    <div class="card">
        <h3>Add Candidate</h3>
       <form method="POST">
    <input type="text" name="candidate_name" placeholder="Enter Candidate Name" required>
    <input type="text" name="party_name" placeholder="Enter Party Name" required>
    <br>
    <button type="submit" name="add_candidate">Add Candidate</button>
    </form>
    </div>

    <div class="card">
        <h3>Candidate List</h3>

        <?php
        if(isset($_POST['add_candidate'])){
    $name = $_POST['candidate_name'];
    $party = $_POST['party_name'];

    $query = "INSERT INTO candidates (name, party)
              VALUES ('$name', '$party')";

    mysqli_query($conn, $query);
}

        $result = mysqli_query($conn,"SELECT * FROM candidates");

        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Party</th><th>Action</th></tr>";

        while($row = mysqli_fetch_assoc($result)){
    echo "<tr>";
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['party']."</td>";
    echo "<td>";
    echo "<a href='admin_dashboard.php?delete_id=".$row['id']."' class='danger'>Delete</a>";
    echo "</td>";
    echo "</tr>";
}
        echo "</table>";
        ?>
    </div>

    <a href="admin_reset_votes.php">
        <button class="danger">Reset All Votes</button>
    </a>

    <br>

    <a href="logout.php">
        <button class="logout">Logout</button>
    </a>

</div>

</body>
</html>