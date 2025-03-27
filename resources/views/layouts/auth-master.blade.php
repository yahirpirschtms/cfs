<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMS CFS</title>
    <link rel="icon" type="image/png" href="{!! asset('/icons/tms_logo.jpg')!!}">
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js'
        ])

        <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(rgb(13, 57, 135),rgb(13, 50, 98));
            height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 360px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
        }

        .header {
            position: relative;
            background: linear-gradient(135deg, #1e4877,rgb(13, 82, 200));
            padding: 40px 0 20px;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            width: 100%;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            transform: scale(1.5, 1);
            z-index: 1;
        }

        .title {
            font-size: 24px;
            margin: 10px 0 0;
            z-index: 2;
            position: relative;
        }

        .btn-style{
            padding: 10px;
            background-color: rgb(13, 82, 200);
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-style:hover {
            background-color: #2575fc;
            color: #ddd;
        }
    </style>

</head>
<body>
    <div class="container h-100 d-flex justify-content-center align-items-center">
        @yield('content') 
    </div>
</body>
</html>