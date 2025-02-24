<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilihan Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #e0f7fa, #b2ebf2);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
        }

        .button {
            background-color: #4dd0e1;
            color: #fff;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin: 10px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
        }

        .button:hover {
            background-color: #26c6da;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #01579b;
            margin-bottom: 20px;
        }

        .scrolling-text {
            margin-top: 20px;
            font-size: 16px;
            color: #0277bd;
            white-space: nowrap;
            overflow: hidden;
            animation: scrollText 5s linear infinite;
            position: absolute;
            bottom: 10px;
            width: 100%;
        }

        @keyframes scrollText {
            0% {
                transform: translateX(100%);
            }
            500% {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Menu Koreksi</h1>
        <a href="upload.php" class="button">Upload Koreksi</a>
        <a href="mphlbjs.php" class="button">MPHLBJS</a>
    </div>

    <div class="scrolling-text">KPPN Kolaka</div>

</body>
</html>
