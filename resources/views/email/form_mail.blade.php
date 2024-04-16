<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>

    <style>
        .body {
            padding: 0;
            margin: 0;
            height: 100%;
            width: 100%;
        }

        .container {
            text-align: center;
            margin: 0 auto;
            max-width: 600px;
            display: block;
            font-family: inherit;
            background-color: #f0f0f0;
            padding: 20px;
        }

        .container h2 {
            font-size: 32px;
            font-weight: 500;
            letter-spacing: 0.01em;
            color: #141212;
            text-align: center;
            line-height: 39px;
            margin: 0;
            font-family: inherit;
        }

        .container .otp span,
        .container h1,
        .container h2 {
            text-align: center;
        }

        .container .otp {
            margin-top: 30px;
            margin-bottom: 30px;
            background-color: white;
            padding: 50px;
        }

        .container .otp p {
            margin: 0;
            padding: 0;
            font-weight: 500;
            font-size: 18px;
            line-height: 140%;
            letter-spacing: -0.01em;
            color: #666;
            font-family: inherit;
            text-align: center;
        }

        .container .otp h1 {
            margin: 20px 0 0 0;
            padding: 0;
            border: none;
            border-spacing: 0;
            line-height: 100%;
            text-align: center;
            /* font-size: 37px; */
            line-height: 100%;
            text-transform: uppercase;
            letter-spacing: 0.7em;
            border-collapse: collapse;
            font-family: inherit;
        }

        .container .time {
            margin: 0;
            padding: 0;
            border: none;
            border-spacing: 0;
            text-align: center;
            border-collapse: collapse;
            font-family: inherit;
        }

        img {
            width: 200px;
            height: 150px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <div class="body">
        <div class="container">
            <img src="{{ $message->embed(public_path('/logo/logo.png')) }}" alt="logo">

            <div class="otp">
                <span>{{ __('here_code') }}</span>
                <h1>{{ $otp }}</h1>
            </div>

            <span class="time">*{{ __('Code_time') }}.</span>
        </div>
    </div>
</body>

</html>
