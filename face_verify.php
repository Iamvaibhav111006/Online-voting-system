<!DOCTYPE html>
<html>
<head>
    <title>Face Verification</title>

    <!-- ✅ Put face-api.js here -->
    <script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>

</head>

<body>

    <!-- UI -->
    <video id="video" width="400" height="300" autoplay></video>
    <br>
    <button id="checkBtn" type="button">Check Face</button>
    <p id="status"></p>

    <!-- ✅ Put ALL JS code here -->
     <script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
window.onload = async function(){

    const video = document.getElementById('video');
    const status = document.getElementById('status');
    const button = document.getElementById('checkBtn');

    // Start camera
    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(() => status.innerHTML = "Camera error ❌");

    // Load models
    try{
        await faceapi.nets.tinyFaceDetector.loadFromUri('models');
        await faceapi.nets.faceLandmark68Net.loadFromUri('models');
        await faceapi.nets.faceRecognitionNet.loadFromUri('models');

        status.innerHTML = "Models Loaded ✅";
    }catch(error){
        status.innerHTML = "Model Load Error ❌";
        console.log(error);
    }

    // Button click event
    button.addEventListener("click", async function(){

        try{
            status.innerHTML = "Checking...";

            const detection = await faceapi.detectSingleFace(
                video,
                new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks().withFaceDescriptor();

            if(detection){
                status.innerHTML = "Face + Descriptor Working ✅";
            }else{
                status.innerHTML = "Face Not Detected ❌";
            }

        }catch(error){
            status.innerHTML = "Error: " + error;
            console.log(error);
        }

    });

}
</script>

</body>
</html>