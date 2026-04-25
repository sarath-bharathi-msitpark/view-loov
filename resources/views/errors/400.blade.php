<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .error_container {
            text-align: center;
            padding: 20px;
        }

        .error_img {
            max-width: 500px;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .error_title {
            font-size: 50px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111;
        }

        .error_text {
            font-size: 23px;
            color: #000;
            margin-bottom: 30px;
        }

        .btn_error {
            min-width: 120px;
            border-radius: 30px;
            font-weight: 500;
            padding: 10px 20px;
        }

        @media screen and (max-width: 576px) {
            .error_title {
                font-size: 30px !important;
            }

            .error_text {
                font-size: 20px !important;
            }
        }
    </style>
</head>

<body>

<div class="error_container">

    <h1 class="error_title">OOPS!</h1>
    <p class="error_text">The Page You Requested Could Not Be Found.</p>

    <img src="{{ asset('assets/assestsnew/errorImages/400_error.svg') }}" alt="404 Error" class="error_img">
    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary btn_error">Go Back</a>
        <a href="{{ url('/') }}" class="btn btn-primary btn_error">Home</a>
    </div>
</div>

</body>

</html>
