<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Face Login</title>
<script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    *{ box-sizing:border-box; }

    body{
        margin:0;
        min-height:100vh;
        font-family:"Archivo", sans-serif;
        color:#dff8ff;
        background:
            radial-gradient(circle at top left, rgba(14,165,233,0.18), transparent 24%),
            linear-gradient(135deg, #06131a 0%, #09232d 60%, #0e3a4a 100%);
    }

    .page{
        min-height:100vh;
        display:grid;
        place-items:center;
        padding:24px;
    }

    .shell{
        width:min(1100px, 100%);
        display:grid;
        grid-template-columns:360px minmax(0, 1fr);
        gap:24px;
    }

    .info,
    .panel{
        border-radius:30px;
        border:1px solid rgba(255,255,255,0.08);
        box-shadow:0 24px 60px rgba(0,0,0,0.24);
    }

    .info{
        padding:30px;
        background:rgba(255,255,255,0.06);
    }

    .info h1{
        margin:0 0 12px;
        font-size:38px;
        line-height:1;
    }

    .info p{
        margin:0;
        color:rgba(223,248,255,0.72);
        line-height:1.8;
    }

    .steps{
        display:grid;
        gap:14px;
        margin-top:24px;
    }

    .step{
        padding:14px 16px;
        border-radius:18px;
        background:rgba(255,255,255,0.05);
    }

    .step strong{
        display:block;
        margin-bottom:6px;
        font-size:15px;
    }

    .step span{
        color:rgba(223,248,255,0.70);
        font-size:13px;
        line-height:1.7;
    }

    .panel{
        background:rgba(7,27,35,0.92);
        padding:28px;
    }

    .panel-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
        margin-bottom:18px;
    }

    .panel-head h2{
        margin:0 0 8px;
        font-size:28px;
    }

    .panel-head p{
        margin:0;
        color:rgba(223,248,255,0.66);
        line-height:1.7;
        font-size:14px;
    }

    .status-pill{
        padding:9px 12px;
        border-radius:999px;
        background:rgba(14,165,233,0.12);
        color:#7dd3fc;
        font-size:12px;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:0.06em;
    }

    .field{
        margin-bottom:16px;
    }

    .field input{
        width:100%;
        height:54px;
        padding:0 16px;
        border-radius:16px;
        border:1px solid rgba(255,255,255,0.12);
        background:rgba(255,255,255,0.06);
        color:#fff;
        font:inherit;
    }

    .camera{
        width:100%;
        border-radius:26px;
        display:block;
        background:#020617;
        overflow:hidden;
        border:1px solid rgba(255,255,255,0.08);
    }

    .camera video{
        width:100%;
        display:block;
        aspect-ratio:16 / 10;
        object-fit:cover;
    }

    .actions{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        margin-top:18px;
    }

    .verify-btn{
        min-width:220px;
        height:54px;
        border:none;
        border-radius:18px;
        background:linear-gradient(135deg, #06b6d4, #0284c7);
        color:white;
        font:inherit;
        font-weight:800;
        cursor:pointer;
    }

    .status-text{
        color:#a5f3fc;
        font-size:14px;
        line-height:1.6;
    }

    @media (max-width: 920px){
        .shell{ grid-template-columns:1fr; }
    }

    @media (max-width: 640px){
        .page{ padding:16px; }
        .info, .panel{ padding:22px; }
        .actions{ flex-direction:column; align-items:stretch; }
        .verify-btn{ width:100%; }
    }
</style>
</head>
<body>
<main class="page">
    <section class="shell">
        <div class="info">
            <h1>Face verification</h1>
            <p>Complete the final identity check before you enter the ballot page.</p>

            <div class="steps">
                <div class="step">
                    <strong>1. Enter username</strong>
                    <span>Use the same voter username you used during login.</span>
                </div>
                <div class="step">
                    <strong>2. Position your face</strong>
                    <span>Stay centered and keep your face clearly visible to the camera.</span>
                </div>
                <div class="step">
                    <strong>3. Verify and continue</strong>
                    <span>A matched face will move you directly to the voting page.</span>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div>
                    <h2>Identity Check</h2>
                    <p>When the system recognizes your registered face, you will be redirected to vote.</p>
                </div>
                <span class="status-pill">Live Camera</span>
            </div>

            <div class="field">
                <input type="text" id="username" placeholder="Enter username">
            </div>

            <div class="camera">
                <video id="video" autoplay muted></video>
            </div>

            <div class="actions">
                <button class="verify-btn" onclick="verifyFace()">Verify Face</button>
                <p id="status" class="status-text">Loading camera and face models...</p>
            </div>
        </div>
    </section>
</main>

<script>
let modelsLoaded = false;
let attempts = 0;
const maxAttempts = 3;

window.addEventListener('load', async () => {
    const video = document.getElementById('video');
    const statusText = document.getElementById('status');

    try{
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    }catch(e){
        statusText.innerHTML = "Camera error";
        return;
    }

    try{
        await faceapi.nets.tinyFaceDetector.loadFromUri('models');
        await faceapi.nets.faceLandmark68Net.loadFromUri('models');
        await faceapi.nets.faceRecognitionNet.loadFromUri('models');

        statusText.innerHTML = "Models loaded. Ready for verification.";
        modelsLoaded = true;
    }catch(e){
        console.log(e);
        statusText.innerHTML = "Model load error";
    }
});

async function verifyFace(){
    const username = document.getElementById("username").value;
    const statusText = document.getElementById("status");
    const video = document.getElementById("video");

    if(username == ""){
        alert("Enter username first");
        return;
    }

    if(!modelsLoaded){
        statusText.innerHTML = "Please wait while the models finish loading.";
        return;
    }

    statusText.innerHTML = "Checking face...";

    try{
        const detection = await faceapi.detectSingleFace(
            video,
            new faceapi.TinyFaceDetectorOptions()
        ).withFaceLandmarks().withFaceDescriptor();

        if(!detection){
            statusText.innerHTML = "Face not detected.";
            return;
        }

        const liveDescriptor = Array.from(detection.descriptor);

        const response = await fetch("face_check.php", {
            method: "POST",
            credentials: "same-origin",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({
                username: username,
                face: liveDescriptor
            })
        });

        const result = await response.text();
        console.log("SERVER:", result);

        if(result.trim() === "success"){
            attempts = 0;
            statusText.innerHTML = "Face matched. Redirecting to ballot...";
            window.location.href = "vote.php";
        }else{
            attempts++;

            if(attempts >= maxAttempts){
                statusText.innerHTML = "Too many attempts. Access blocked.";
                document.querySelector(".verify-btn").disabled = true;
            }else{
                statusText.innerHTML = "Face not matched. Attempt: " + attempts;
            }
        }

    }catch(error){
        console.log(error);
        statusText.innerHTML = "Verification error";
    }
}
</script>

</body>
</html>
