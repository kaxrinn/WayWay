<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Partner Application</title>
</head>
<body style="font-family: 'Poppins', Arial, sans-serif; background-color: #f4f6f9; padding: 30px; margin: 0;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06);">

        <div style="background: linear-gradient(135deg, #508db1, #9eccdb); padding: 24px 30px;">
            <h1 style="color: #ffffff; margin: 0; font-size: 20px;">WayWay — New Partner Application</h1>
        </div>

        <div style="padding: 30px;">
            <p style="font-size: 14px; color: #333; margin-bottom: 20px;">
                A new partner registration has been submitted. Please review the details below and contact the applicant via their email.
            </p>

            <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #333;">
                <tr>
                    <td style="padding: 10px 0; width: 160px; font-weight: 600; vertical-align: top;">Partner Type</td>
                    <td style="padding: 10px 0;">{{ $data['partner_type'] }}</td>
                </tr>
                <tr style="border-top: 1px solid #eee;">
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Full Name</td>
                    <td style="padding: 10px 0;">{{ $data['name'] }}</td>
                </tr>
                <tr style="border-top: 1px solid #eee;">
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Business / Agency Name</td>
                    <td style="padding: 10px 0;">{{ $data['business_name'] }}</td>
                </tr>
                <tr style="border-top: 1px solid #eee;">
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Email</td>
                    <td style="padding: 10px 0;"><a href="mailto:{{ $data['email'] }}" style="color: #508db1;">{{ $data['email'] }}</a></td>
                </tr>
                <tr style="border-top: 1px solid #eee;">
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">WhatsApp</td>
                    <td style="padding: 10px 0;">{{ $data['whatsapp'] }}</td>
                </tr>
                @if (!empty($data['link']))
                <tr style="border-top: 1px solid #eee;">
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Website / Instagram</td>
                    <td style="padding: 10px 0;">{{ $data['link'] }}</td>
                </tr>
                @endif
                <tr style="border-top: 1px solid #eee;">
                    <td style="padding: 10px 0; font-weight: 600; vertical-align: top;">Description</td>
                    <td style="padding: 10px 0;">{{ $data['description'] }}</td>
                </tr>
            </table>

            <p style="font-size: 12px; color: #999; margin-top: 30px;">
                This email was sent automatically from the WayWay partner registration form.
            </p>
        </div>
    </div>

</body>
</html>