<?php
session_start();
include("db.php");

$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$live_face = $data['face'];

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE username='$username'"));

if(!$user){
    echo "fail";
    exit();
}

$stored_face = json_decode($user['face_data'], true);

function faceDistance($a, $b){
    $sum = 0;
    for($i=0; $i<count($a); $i++){
        $sum += pow($a[$i] - $b[$i], 2);
    }
    return sqrt($sum);
}

$distance = faceDistance($live_face, $stored_face);

if($distance < 0.35){

    // ✅ ADD THIS LINE
    $_SESSION['username'] = $username;
    $_SESSION['face_verified'] = true;
    echo "success";

}else{
    echo "fail";
}
?>