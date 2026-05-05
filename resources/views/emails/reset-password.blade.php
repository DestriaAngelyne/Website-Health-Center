<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #1976D2;
            padding: 24px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 20px;
        }
        .body {
            padding: 32px 24px;
            text-align: center;
        }
        .body p {
            color: #555555;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 16px;
        }
        .otp-box {
            display: inline-block;
            background-color: #f0f4ff;
            border: 2px dashed #1976D2;
            border-radius: 8px;
            padding: 16px 32px;
            margin: 16px 0;
        }
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            color: #1976D2;
            letter-spacing: 8px;
        }
        .warning {
            color: #e53935;
            font-size: 13px;
            margin-top: 16px;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 16px 24px;
            text-align: center;
        }
        .footer p {
            color: #999999;
            font-size: 12px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Graha Medika</h1>
        </div>
        <div class="body">
            <p>Halo, <strong>{{ $user->name }}</strong>!</p>
            <p>Kami menerima permintaan reset password untuk akun kamu.<br>Gunakan kode OTP berikut:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
            </div>

            <p>Kode ini berlaku selama <strong>10 menit</strong>.</p>
            <p class="warning">Jangan bagikan kode ini kepada siapapun.<br>Tim Graha Medika tidak akan pernah meminta kode OTP kamu.</p>
        </div>
        <div class="footer">
            <p>Jika kamu tidak merasa meminta reset password, abaikan email ini.</p>
            <p>&copy; {{ date('Y') }} Graha Medika. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
