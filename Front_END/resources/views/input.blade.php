@extends('layouts.app')

@section('title', 'Input Foto and Data')

@section('content')
<div class="card-container">
    <div class="card shadow container">
        <div class="row">
            <div class="col-custom-80">
                <div class="col align-self-center">
                    <div id="previewContainer" style="display: none; margin-top: 20px;">
                        <h2>Preview Result</h2>
                        <p><strong>Name:</strong> <span id="previewName"></span></p>
                        <p><strong>Detection Flag:</strong> <span id="previewDetect"></span></p>
                        <p><strong>Similarity Percentage:</strong> <span id="previewSimilarity"></span>%</p>
                        <img id="previewImage" src="" alt="Image with Landmarks" style="max-width: 100%; height: auto;">
                        <button id="backToFormButton" style="padding: 10px 20px; font-size: 16px;">Back to Form</button>
                    </div>
                    <img id="croppedImage" style="display: none;">
                    <input type="file" id="uploadInput" accept="image/*" style="display: none;">
                </div>
                <div class="row align-items-end border border-danger h-100 d-flex justify-content-center">
                    <div id="webcamContainer" style=" position: relative; width: 640px; height: 640px; margin-bottom: 20px; display: none;">
                        <div class="spinner-grow text-primary justify-content-center" id="video-loading" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        <video id="video" width="640" autoplay style=" position: absolute; top: 0; left: 0;"></video>
                        <button id="captureButton" class="btn btn-secondary" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); padding: 10px; font-size: 16px;">📷</button>
                    </div>
                    <div id="cropContainer" class="mt-2 mb-3 card shadow" style="display: none;">
                        <button id="rotateLeftButton" class="btn btn-info" style="padding: 7px; font-size: 16px;">⟲</button>
                        <button id="rotateRightButton" class="btn btn-info" style="padding: 7px; font-size: 16px;">⟳</button>
                        <button id="saveCropButton" class="btn btn-success" style="padding: 5px; font-size: 16px;">Save Crop</button>
                    </div>
                    <div class="row border border-warning">
                        <button id="backButton" class="btn btn-warning" style="padding: 10px; font-size: 16px; display: none;">Back</button>
                        <div class="col align-self-center border">
                            <div id="sourceSelection" class="border d-flex justify-content-center p-2">
                                <button id="webcamButton" class="btn btn-secondary mx-2" style="padding: 10px; font-size: 16px;">Use Webcam</button>
                                <button id="uploadButton" class="btn btn-secondary" style="padding: 10px; font-size: 16px;">Browse Image</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-custom-20 align-self-center">
                <form id="photoForm" method="POST">
                    @csrf
                    <input type="hidden" name="image" id="imageInput">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="diagnose">Diagnosa</label>
                        <input type="checkbox" class="form-check-label" id="diagnose" name="detectio">
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-size: 16px;">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const webcamButton = document.getElementById('webcamButton');
    const uploadButton = document.getElementById('uploadButton');
    const video_loading = document.getElementById('video-loading');
    const webcamContainer = document.getElementById('webcamContainer');
    const video = document.getElementById('video');
    const captureButton = document.getElementById('captureButton');
    const uploadInput = document.getElementById('uploadInput');
    const uploadedImage = document.getElementById('uploadedImage');
    const croppedImage = document.getElementById('croppedImage');
    const cropContainer = document.getElementById('cropContainer');
    const saveCropButton = document.getElementById('saveCropButton');
    const rotateLeftButton = document.getElementById('rotateLeftButton');
    const rotateRightButton = document.getElementById('rotateRightButton');
    const backButton = document.getElementById('backButton');
    const imageInput = document.getElementById('imageInput');
    const sourceSelection = document.getElementById('sourceSelection');
    const previewContainer = document.getElementById('previewContainer');
    const previewName = document.getElementById('previewName');
    const previewDetect = document.getElementById('previewDetect');
    const previewSimilarity = document.getElementById('previewSimilarity');
    const previewImage = document.getElementById('previewImage');
    const backToFormButton = document.getElementById('backToFormButton');
    let cropper;

    const constraints = {
        video: {
            width: { ideal: 640 },
            height: { ideal: 640 },
            aspectRatio: 1
        }
    };

    webcamButton.addEventListener('click', () => {
        video_loading.style.display ='block';
        webcamButton
        navigator.mediaDevices.getUserMedia(constraints)
            .then(mediaStream => {
                stream = mediaStream;
                video.srcObject = stream;
                video_loading.style.display ='none';
                video.style.display = 'block';
            })
            .catch(error => {
                console.error('Error accessing media devices.', error);
            });
        sourceSelection.style.display = 'none';
        webcamContainer.style.display = 'block';
        backButton.style.display = 'block';
        uploadInput.style.display = 'none';
        uploadedImage.style.display = 'none';
        cropContainer.style.display = 'none';
    });

    uploadButton.addEventListener('click', () => {
        sourceSelection.style.display = 'none';
        webcamContainer.style.display = 'none';
        video.style.display = 'none';
        backButton.style.display = 'block';
        cropContainer.style.display = 'none';
        uploadInput.click();
    });

    uploadInput.addEventListener('change', function() {
        const file = this.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            if (cropper) {
                cropper.destroy();
            }

            uploadedImage.src = e.target.result;
            uploadedImage.style.display = 'block';
            cropContainer.style.display = 'block';

            cropper = new Cropper(uploadedImage, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                crop(event) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 640,
                        height: 640
                    });
                    croppedImage.src = canvas.toDataURL('image/jpeg');
                    imageInput.value = canvas.toDataURL('image/jpeg');
                }
            });
        };

        reader.readAsDataURL(file);
    });

    saveCropButton.addEventListener('click', () => {
        const canvas = cropper.getCroppedCanvas({
            width: 640,
            height: 640
        });
        croppedImage.src = canvas.toDataURL('image/jpeg');
        imageInput.value = canvas.toDataURL('image/jpeg');
    });

    rotateLeftButton.addEventListener('click', () => {
        cropper.rotate(-90);
    });

    rotateRightButton.addEventListener('click', () => {
        cropper.rotate(90);
    });

    backButton.addEventListener('click', () => {
        stream.getTracks().forEach(function(track) {
  track.stop();
});
        backButton.style.display = 'none';
        sourceSelection.style.display = 'block';
        webcamContainer.style.display = 'none';
        video.style.display = 'none';   
        uploadInput.style.display = 'none';
        uploadedImage.style.display = 'none';
        cropContainer.style.display = 'none';
    });

    document.getElementById('photoForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('{{ route("photo.submit") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('previewName').innerText = data.name;
            document.getElementById('previewDetect').innerText = data.detect ? 'Yes' : 'No';
            document.getElementById('previewSimilarity').innerText = data.similarity_percentage;
            document.getElementById('previewImage').src = 'data:image/png;base64,' + data.image_with_landmarks;

            document.getElementById('photoForm').style.display = 'none';
            previewContainer.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    backToFormButton.addEventListener('click', () => {
        document.getElementById('photoForm').style.display = 'block';
        previewContainer.style.display = 'none';
    });
</script>
@endsection
