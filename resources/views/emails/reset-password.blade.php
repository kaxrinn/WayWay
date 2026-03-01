<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin:0; padding:40px 20px; font-family:Arial,sans-serif; background:url('{{ asset('assets/background/bgemail.jpg') }}') center/cover; background-attachment:fixed;">
    
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#fff; border-radius:20px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">
        
        <!-- Header -->
        <tr>
            <td style="background:linear-gradient(135deg, #9eccdb 0%, #89b8c9 100%); padding:40px 30px; text-align:center; border-radius:20px 20px 0 0;">
                <img src="https://github.com/kaxrinn/WayWay/blob/main/public/Assets/Logo/logodnnama.png?raw=true" alt="WayWay Logo" style="max-width:180px; height:auto; margin-bottom:15px; filter:drop-shadow(0 2px 8px rgba(0,0,0,0.15));">
                <h1 style="color:#fff; font-size:28px; margin:0; text-shadow:0 2px 4px rgba(0,0,0,0.1);">Reset Your Password</h1>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding:40px 30px;">
                
                <!-- Greeting -->
                <p style="font-size:20px; font-weight:600; color:#4e4e4e; margin:0 0 15px;">
                    Hi, {{ $namaPengguna }}! 👋
                </p>
                
                <p style="font-size:15px; color:#4e4e4e; line-height:1.8; margin:0 0 20px; opacity:0.9;">
                    We received a request to reset the password for your WayWay account. Click the button below to create a new password.
                </p>

                <!-- Info Box -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;">
                    <tr>
                        <td style="background:#f4dbb4; border-left:4px solid #9eccdb; padding:15px; border-radius:8px;">
                            <p style="font-size:14px; color:#4e4e4e; margin:0;">
                                <strong>⏱️ Link valid for 60 minutes</strong><br>
                                Make sure to reset your password before the link expires.
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Button -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin:30px 0;">
                    <tr>
                        <td style="text-align:center;">
                            <a href="{{ $url }}" style="display:inline-block; padding:16px 40px; background:#9eccdb; color:#fff; text-decoration:none; border-radius:10px; font-weight:600; font-size:16px; box-shadow:0 4px 12px rgba(158,204,219,0.4); transition:all 0.3s;">
                                Reset Password
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Alternative Link -->
                <p style="font-size:13px; color:#4e4e4e; margin:20px 0 8px; opacity:0.8;">
                    If the button doesn't work, copy and paste this link:
                </p>
                <p style="font-size:12px; color:#9eccdb; word-break:break-all; margin:0; padding:12px; background:#eeeeee; border-radius:8px; border-left:3px solid #9eccdb;">
                    {{ $url }}
                </p>

                <!-- Security Note -->
                <table width="100%" cellpadding="0" cellspacing="0" style="margin:25px 0 0;">
                    <tr>
                        <td style="background:#f4dbb4; border-left:4px solid #9eccdb; padding:15px; border-radius:8px;">
                            <p style="font-size:13px; color:#4e4e4e; margin:0;">
                                <strong>🔒 Security Note:</strong> If you didn't request a password reset, you can safely ignore this email. Your account remains secure.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background:#4e4e4e; padding:30px; text-align:center; border-top:1px solid #eeeeee; border-radius:0 0 20px 20px;">
                <p style="font-size:12px; color:#eeeeee; margin:10px 0; opacity:0.9;">
                    Thank you for using WayWay.<br>
                    Need help? <a href="mailto:waywaypolibatam@gmail.com" style="color:#9eccdb; text-decoration:none; font-weight:600;">waywaypolibatam@gmail.com</a>
                </p>
                <p style="font-size:11px; color:#eeeeee; margin:15px 0 0; opacity:0.7;">
                    © 2026 WayWay. All rights reserved.
                </p>
            </td>
        </tr>

    </table>

</body>
</html>