<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your One-Time Password (OTP)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:20px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#2563eb; padding:20px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:22px;">
                                {{ config('app.name') }}
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; color:#333333;">
                            <p style="font-size:16px; margin:0 0 15px;">
                                Hello {{ $user?->name ?? 'User' }},
                            </p>

                            <p style="font-size:15px; line-height:1.6; margin:0 0 20px;">
                                We received a request to verify your account.
                                Please use the One-Time Password (OTP) below to continue:
                            </p>

                            <!-- OTP Box -->
                            <div style="text-align:center; margin:30px 0;">
                                <span
                                    style="
                                display:inline-block;
                                font-size:28px;
                                letter-spacing:6px;
                                font-weight:bold;
                                color:#2563eb;
                                padding:15px 25px;
                                border:1px dashed #2563eb;
                                border-radius:6px;
                                background-color:#f8fafc;">
                                    {{ $otp }}
                                </span>
                            </div>

                            <p style="font-size:14px; color:#555; margin:0 0 10px;">
                                This OTP is valid for <strong>{{ $expiresIn ?? '5 minutes' }}</strong>.
                            </p>

                            <p style="font-size:14px; color:#555; margin:0 0 20px;">
                                If you did not request this, please ignore this email.
                            </p>

                            <p style="font-size:14px; margin:0;">
                                Regards,<br>
                                <strong>{{ config('app.name') }} Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#666;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
