<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #201f2d;
            border-radius: 5px;
        }

        .content {
            padding: 20px;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            color: #ffffff;
        }

        .header {
            text-align: center;
            padding: 20px;
        }

        .button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            font-size: 16px;
            color: #ffffff !important;
            background-color: #ff5722;
            text-decoration: none !important;
            border-radius: 5px;
        }

        .code {
            padding-top: 5px;
        }

        .code strong {
            background-color: #201f2d;
            padding: 10px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #666666;
        }
    </style>
</head>
<body>
<table class="container">
    <tr>
        <td class="header">
            <img src="{{ url('images/logotext.png') }}" alt="rocker.am" width="200">
        </td>
    </tr>
    <tr>
        <td class="content">
            <p>Hey {{$user->name}}</p>
            <p>Please verify your email address by clicking the button below:</p>
            <a href="{{$url}}" class="button">Verify Email</a>
            <p>Alternatively, use the following verification code:</p>
            <p class="code"><strong>{{$code}}</strong></p>
            <p>If you did not sign up for this course, you can safely ignore this email.</p>
        </td>
    </tr>
    <tr>
        <td class="footer">
            © {{ date('Y') }}  {{config('app.name')}}. Some rights reserved.
        </td>
    </tr>
</table>
</body>
</html>
