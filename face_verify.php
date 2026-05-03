<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Verification</title>
    <script defer src="https://unpkg.com/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{ box-sizing:border-box; }

        body{
            margin:0;
            min-height:100vh;
            font-family:"Work Sans", sans-serif;
            color:#202938;
            background:
                radial-gradient(circle at top left, rgba(99,102,241,0.16), transparent 24%),
                linear-gradient(180deg, #f8faff 0%, #eef4ff 100%);
        }

        .page{
            min-height:100vh;
            display:grid;
            place-items:center;
            padding:24px;
        }

        .panel{
            width:min(760px, 100%);
            background:#fff;
            border-radius:32px;
            padding:28px;
            box-shadow:0 28px 60px rgba(66, 79, 118, 0.14);
            border:1px solid #dce6f8;
            text-align:center;
        }

        .panel h1{
            margin:0 0 10px;
            font-size:34px;
        }

        .panel p{
            margin:0 0 20px;
            color:#61708b;
            line-height:1.8;
        }

        video{
            width:100%;
            border-radius:26px;
            background:#111827;
            display:block;
        }

        button{
            margin-top:18px;
            min-width:200px;
            height:52px;
            border:none;
            border-radius:18px;
            background:linear-gradient(135deg, #6366f1, #4f46e5);
            color:#fff;
            font:inherit;
            font-weight:700;
            cursor:pointer;
        }

        #status{
            margin-top:16px;
            color:#4338ca;
            font-weight:600;
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="panel">
            <h1>Face Verification Check</h1>
            <p>Use this page to confirm that camera access and face descriptor detection are working correctly.</p>

            <video id="video" width="400" height="300" autoplay></video>
            <button id="checkBtn" type="button">Check Face</button>
            <p id="status">Loading face models...</p>
        </section>
    </main>

<script>
window.onload = async function(){

    const video = document.getElementById('video');
    const status = document.getElementById('status');
    const button = document.getElementById('checkBtn');

    navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => video.srcObject = stream)
    .catch(() => status.innerHTML = "Camera error");

    try{
        await faceapi.nets.tinyFaceDetector.loadFromUri('models');
        await faceapi.nets.faceLandmark68Net.loadFromUri('models');
        await faceapi.nets.faceRecognitionNet.loadFromUri('models');

        status.innerHTML = "Models loaded. Ready to test.";
    }catch(error){
        status.innerHTML = "Model load error";
        console.log(error);
    }

    button.addEventListener("click", async function(){
        try{
            status.innerHTML = "Checking...";

            const detection = await faceapi.detectSingleFace(
                video,
                new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks().withFaceDescriptor();

            if(detection){
                status.innerHTML = "Face and descriptor are working.";
            }else{
                status.innerHTML = "Face not detected.";
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
