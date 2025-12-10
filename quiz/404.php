<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Not Found</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #282c34;
            color: white;
            text-align: center;
            padding: 50px;
        }

        .error-container {
            max-width: 600px;
            margin: auto;
        }

        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #dc3545;
        }

        .error-message {
            font-size: 24px;
            margin-top: 20px;
        }

        .svg-container {
            margin-top: 50px;
        }

        .glitch {
            display: inline-block;
            font-family: 'Montserrat', sans-serif;
            font-weight: bold;
            text-transform: uppercase;
            position: relative;
        }

        .glitch::before,
        .glitch::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .glitch::before {
            left: 2px;
            text-shadow: -2px 0 red;
            clip: rect(44px, 450px, 56px, 0);
            animation: glitch-anim-1 1s infinite linear alternate-reverse;
        }

        .glitch::after {
            left: -2px;
            text-shadow: -2px 0 blue;
            clip: rect(44px, 450px, 56px, 0);
            animation: glitch-anim-2 1.5s infinite linear alternate-reverse;
        }

        @keyframes glitch-anim-1 {
            0% {
                transform: translate(2px, -2px);
            }

            100% {
                transform: translate(-2px, 2px);
            }
        }

        @keyframes glitch-anim-2 {
            0% {
                transform: translate(-2px, 2px);
            }

            100% {
                transform: translate(2px, -2px);
            }
        }
    </style>
</head>

<body>

    <div class="container error-container">
        <div class="glitch" data-text="Quiz Not Found">Quiz Not Found</div>
        <div class="error-message">The quiz you are looking for is either under construction or has been disabled.</div>

        <!-- SVG Image -->
        <div class="svg-container">
            <svg xmlns="http://www.w3.org/2000/svg" height="200" viewBox="0 0 24 24" width="200" fill="#dc3545">
                <path d="M0 0h24v24H0z" fill="none" />
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 6h2v6h-2zm0 8h2v2h-2z" />
            </svg>
        </div>

        <p class="mt-3">You can check other quizzes or contact the administrator for more information.</p>
        <a class="btn btn-primary mt-3" href="https://comdev.my.id">Go to Home</a>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>