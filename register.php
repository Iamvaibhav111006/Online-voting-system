<?php
session_start();
include("db.php");

if(isset($_POST['register'])){

    $full_name = trim($_POST['full_name']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $password  = trim($_POST['password']);
    $confirm   = trim($_POST['confirm_password']);

    if($password !== $confirm){
        echo "<script>alert('Passwords do not match!');</script>";
    }
    elseif(strlen($password) < 6){
        echo "<script>alert('Password must be at least 6 characters!');</script>";
    }
    else{

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $check = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($check) > 0){
            echo "<script>alert('Username or Email already exists!');</script>";
        }
        else{

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $photo = $_POST['photo'];
            $photo = str_replace('data:image/png;base64,', '', $photo);
            $photo = str_replace(' ', '+', $photo);
            $imageData = base64_decode($photo);

            if(!is_dir("uploads")){
                mkdir("uploads");
            }

            $fileName = "uploads/" . $username . ".png";
            file_put_contents($fileName, $imageData);

            $face_data = $_POST['face_data'];

            $stmt = mysqli_prepare($conn, "INSERT INTO users (full_name, username, email, password, photo, face_data) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $full_name, $username, $email, $hashed_password, $fileName, $face_data);
            mysqli_stmt_execute($stmt);

            echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Voter Registration</title>
<script src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    min-height:100vh;
    font-family:"Outfit", sans-serif;
    color:#1f1630;
    background:
        radial-gradient(circle at top left, rgba(236,72,153,0.18), transparent 24%),
        radial-gradient(circle at right 20%, rgba(59,130,246,0.16), transparent 22%),
        linear-gradient(180deg, #fff8fd 0%, #f7f4ff 100%);
}

*{ box-sizing:border-box; }

.page{
    min-height:100vh;
    display:grid;
    place-items:center;
    padding:24px;
}

.shell{
    width:min(1200px, 100%);
    display:grid;
    grid-template-columns:minmax(0, 1.1fr) 420px;
    gap:24px;
}

.register-box,
.camera-box{
    background:rgba(255,255,255,0.92);
    border:1px solid #eadff6;
    border-radius:30px;
    box-shadow:0 26px 60px rgba(73, 32, 102, 0.10);
}

.register-box{
    padding:30px;
}

.camera-box{
    padding:24px;
    align-self:start;
    position:sticky;
    top:24px;
}

h1{
    margin:0 0 10px;
    font-size:38px;
}

.intro{
    margin:0 0 24px;
    color:#6f5d87;
    line-height:1.8;
    font-size:16px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:16px;
}

.input-group{
    margin-bottom:16px;
}

.input-group.full{
    grid-column:1 / -1;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    font-size:14px;
    font-weight:600;
}

.input-group input{
    width:100%;
    height:54px;
    padding:0 16px;
    border-radius:16px;
    border:1px solid #dfd1ef;
    background:#fff;
    font:inherit;
}

.input-group input:focus{
    outline:none;
    border-color:#c084fc;
    box-shadow:0 0 0 4px rgba(192,132,252,0.12);
}

.register-btn{
    width:100%;
    height:56px;
    border:none;
    border-radius:18px;
    background:linear-gradient(135deg, #a855f7, #ec4899);
    color:white;
    font:inherit;
    font-weight:700;
    cursor:pointer;
    margin-top:8px;
}

.camera-box h2{
    margin:0 0 8px;
    font-size:26px;
}

.camera-box p{
    margin:0 0 18px;
    color:#6f5d87;
    line-height:1.7;
    font-size:14px;
}

video{
    width:100%;
    max-width:100%;
    border-radius:24px;
    background:#120d19;
    display:block;
}

.capture-btn{
    width:100%;
    height:50px;
    margin-top:16px;
    border:none;
    border-radius:16px;
    background:#312e81;
    color:white;
    font:inherit;
    font-weight:700;
    cursor:pointer;
}

.hint{
    margin-top:14px;
    padding:14px 16px;
    border-radius:18px;
    background:#faf5ff;
    color:#6b21a8;
    font-size:13px;
    line-height:1.7;
}

@media (max-width: 980px){
    .shell{ grid-template-columns:1fr; }
    .camera-box{ position:static; }
}

@media (max-width: 720px){
    .page{ padding:16px; }
    .register-box, .camera-box{ padding:22px; }
    .grid{ grid-template-columns:1fr; }
}
</style>
</head>

<body>
<main class="page">
    <section class="shell">
        <div class="register-box">
            <h1>Create your voter account</h1>
            <p class="intro">Complete your registration details, then capture your face data so your identity can be verified before voting.</p>

            <form method="post">
                <div class="grid">
                    <div class="input-group full">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Choose a username" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Create a password" required>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    </div>
                </div>

                <input type="hidden" name="photo" id="photo">
                <input type="hidden" name="face_data" id="face_data">

                <button type="submit" name="register" class="register-btn">Complete Registration</button>
            </form>
        </div>

        <aside class="camera-box">
            <h2>Capture Face</h2>
            <p>Keep your face centered and make sure lighting is clear before saving the image and face descriptor.</p>

            <video id="video" width="320" height="220" autoplay muted></video>
            <button type="button" class="capture-btn" onclick="captureFace()">Capture Face Data</button>
            <canvas id="canvas" style="display:none;"></canvas>

            <div class="hint">Your registration is only complete after face data has been captured successfully.</div>
        </aside>
    </section>
</main>

<script>
const video = document.getElementById('video');

navigator.mediaDevices.getUserMedia({ video: true })
.then(stream => video.srcObject = stream);

async function loadModels(){
    await faceapi.nets.tinyFaceDetector.loadFromUri('models');
    await faceapi.nets.faceLandmark68Net.loadFromUri('models');
    await faceapi.nets.faceRecognitionNet.loadFromUri('models');
    console.log("Models Loaded");
}
loadModels();

async function captureFace(){
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    context.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');
    document.getElementById('photo').value = imageData;

    const detection = await faceapi.detectSingleFace(
        video,
        new faceapi.TinyFaceDetectorOptions()
    ).withFaceLandmarks().withFaceDescriptor();

    if(!detection){
        alert("Face not detected");
        return;
    }

    const descriptor = Array.from(detection.descriptor);
    document.getElementById('face_data').value = JSON.stringify(descriptor);

    alert("Face captured successfully");
}
</script>

</body>
</html>
