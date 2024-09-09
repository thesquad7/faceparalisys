<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <!-- Cropper.js CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <style>
        html, body {
            height: 100%; 
            margin: 0; 
        }
        .card-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh; 
        }
        .card {
            width: 80%; 
            height: 100%; 
            max-width: 95%;
            display: flex;
            flex-direction: column; 
        }
        .row {
            flex: 1;
        }
        .col-custom-80 {
            flex: 0 0 70%;
            height: 100%;
            /* background-color: #00f386;  */
            border: black 2px inset;
            max-width: 70%;
        }
        .col-custom-20 {
            flex: 0 0 30%;
            max-width: 30%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div style="text-align: center; margin-top: 1%; margin-buttom: 4%">
        <h1>Bell's Palsy Detection</h1>
    </div>
    <div class="container">
        @yield('content')
    </div>
    
</body>
</html>