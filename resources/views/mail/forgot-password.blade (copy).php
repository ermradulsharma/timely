<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<html>
<body>
<div align="center">
    <div style="">
        <div style="background-color: #fbfcfd; text-align: left;">
            <div style="margin: 30px;">
                <p>
                    Hello {{ $user_data['name'] ?? "" }},
                </p>

                <p>
                    You are receiving this email because we received a password reset request for your account.
                </p>

                <p>
                    Click <a href="{{ $user_data['link'] ?? "" }}">here</a> to reset password
                </p>

                <p>
                    If you did not request a password reset, no further action is required.
                </p>

                <div style="text-align: left;">
                    Regards,<br>
                    {{ config('app.name') }} App Team
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
