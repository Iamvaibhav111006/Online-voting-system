<!DOCTYPE html>
<html>
<head>
<title>Face Login</title>

<!-- ✅ Correct Face API (browser version) -->
<script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>

</head>

<body style="text-align:center; font-family:Arial;">

<h2>Face Verification Login</h2>

<input type="text" id="username" placeholder="Enter Username"><br><br>

<video id="video" width="300" height="200" autoplay muted></video><br><br>

<button onclick="verifyFace()">Verify Face</button>

<p id="status"></p>

<script>
let modelsLoaded = false;
let attempts = 0;
const maxAttempts = 3;

window.addEventListener('load', async () => {

    const video = document.getElementById('video');
    const statusText = document.getElementById('status');

    // Start camera
    try{
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    }catch(e){
        statusText.innerHTML = "Camera error ❌";
        return;
    }

    // Load models
    try{
        await faceapi.nets.tinyFaceDetector.loadFromUri('models');
        await faceapi.nets.faceLandmark68Net.loadFromUri('models');
        await faceapi.nets.faceRecognitionNet.loadFromUri('models');

        console.log("MODELS LOADED ✅");
        statusText.innerHTML = "Models Loaded ✅";
        modelsLoaded = true;

    }catch(e){
        console.log(e);
        statusText.innerHTML = "Model load error ❌";
    }

});

// 🔥 VERIFY FUNCTION
async function verifyFace(){

    const username = document.getElementById("username").value;
    const statusText = document.getElementById("status");
    const video = document.getElementById("video");

    if(username == ""){
        alert("Enter username first");
        return;
    }

    if(!modelsLoaded){
        statusText.innerHTML = "Wait... Models loading ⏳";
        return;
    }

    statusText.innerHTML = "Checking face...";

    try{
        const detection = await faceapi.detectSingleFace(
            video,
            new faceapi.TinyFaceDetectorOptions()
        ).withFaceLandmarks().withFaceDescriptor();

        if(!detection){
            statusText.innerHTML = "Face not detected ❌";
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

    attempts = 0; // reset attempts
    statusText.innerHTML = "Face matched ✅";
    window.location.href = "vote.php";

}else{

    attempts++; // increase attempts

    if(attempts >= maxAttempts){
        statusText.innerHTML = "Too many attempts ❌ Access Blocked";
        document.querySelector("button").disabled = true;
    }else{
        statusText.innerHTML = "Face not matched ❌ Attempt: " + attempts;
    }
} 

    }catch(error){
        console.log(error);
        statusText.innerHTML = "Error ❌";
    }
}
</script>

</body>
</html>