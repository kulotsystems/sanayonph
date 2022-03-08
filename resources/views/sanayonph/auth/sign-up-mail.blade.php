<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family:-apple-system, BlinkMacSystemFont ,'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #fce205;
            padding: 15px;
        }

        #main {
            padding: 15px;
            background: #fff;
            color: #000;
        }

        .text-monospace {
            font-family: 'Courier New', Consolas, Menlo, Monaco, 'Lucida Console', 'Liberation Mono', 'DejaVu Sans Mono', 'Bitstream Vera Sans Mono', monospace;
        }
    </style>
</head>
<body>
    <div id="main">
        <h3>Hello {{ $details['first_name'] }},</h3>
        <p>
            Thank you for creating your Sanayon Account.
        </p>
        <p>
            Please use the following verification code to complete your registration:
        </p>
        <h1 class="text-monospace">
            {{ $details['otp'] }}
        </h1>
        <hr>
        <p>
            <small>
                Regards,<br>
                Sanayon Dev Team
            </small>
        </p>
    </div>
</body>
</html>
