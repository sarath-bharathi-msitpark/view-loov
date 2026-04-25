<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
@php
    $logo = \App\Models\Utility::get_file('uploads/logo');
    $company_logo = \App\Models\Utility::getValByName('company_logo') ?? 'logo-dark.png';
@endphp
<head>
    <title>New Contact Us Submission</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table, td {
            border-collapse: collapse;
        }

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        p {
            margin: 13px 0;
        }

        .outlook-group-fix {
            width: 100% !important;
        }

        @media only screen and (max-width: 480px) {
            table.full-width-mobile {
                width: 100% !important;
            }

            td.full-width-mobile {
                width: auto !important;
            }
        }
    </style>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
</head>

<body style="background-color:#f0f2f5; font-family: 'Open Sans', sans-serif; margin: 0; padding: 20px;">
<div
    style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
    <div style="text-align: center; padding: 30px 20px 20px;">
        <img alt="Logo"
             src="{{ $logo . '/' . (isset($company_logo) && !empty($company_logo) ? $company_logo : 'logo-dark.png') }}"
             style="max-width: 180px; height: auto;">
    </div>
    <div style="padding: 0 30px 20px;">
        <h2 style="margin: 0 0 20px; font-size: 22px; color: #333;">New Contact Us Submission</h2>
        <table cellpadding="10" cellspacing="0" width="100%"
               style="border-collapse: collapse; color: #555; font-size: 15px;">
            <tr>
                <td width="50%" style="font-weight: 600;">Full Name:</td>
                <td>{{ $data['full_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Phone Number:</td>
                <td>{{ $data['phone_number'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Work Email:</td>
                <td>{{ $data['work_email'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="font-weight: 600;">Company Name:</td>
                <td>{{ $data['company_name'] ?? 'N/A' }}</td>
            </tr>
            @if(isset($data['country']) && $data['country'])
            <tr>
                <td style="font-weight: 600;">Country:</td>
                <td>{{ $data['country'] ?? 'N/A' }}</td>
            </tr>
            @endif
            @if(isset($data['users']) && $data['users'])
            <tr>
                <td style="font-weight: 600;">No of Handy Users / Subscription:</td>
                <td>{{ $data['users'] ?? 'N/A' }}</td>
            </tr>
            @endif
            @if(isset($data['referral']) && $data['referral'])
            <tr>
                <td style="font-weight: 600;">How did you hear about us?</td>
                <td>{{ $data['referral'] ?? 'N/A' }}</td>
            </tr>
            @endif
            @if(isset($data['message']) && $data['message'])
            <tr>
                <td style="font-weight: 600;">Message</td>
                <td>{{ $data['message'] ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>
    </div>
    <div style="padding: 30px; text-align: center; background-color: #f8f8f8; font-size: 13px; color: #999;">
        © {{ date('Y') }} Loov. All rights reserved.
    </div>
</div>
</body>

</html>
