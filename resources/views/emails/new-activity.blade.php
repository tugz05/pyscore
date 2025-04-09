<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Activity Posted</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
        <div style="background-color: #1a73e8; padding: 20px; color: #fff;">
            <h2 style="margin: 0;">📘 New Activity in {{ $class->name ?? 'Your Class' }}</h2>
        </div>
        <div style="padding: 20px;">
            <h3 style="margin-top: 0; color: #333;">{{ $activity->title }}</h3>
            <p style="color: #555;">{!! $activity->instruction ?? 'No instructions provided.' !!}</p>


            <table style="margin-top: 20px; width: 100%;">
                <tr>
                    <td style="font-weight: bold; color: #333;">Due Date:</td>
                    <td style="color: #555;">{{ \Carbon\Carbon::parse($activity->due_date)->format('F j, Y') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #333;">Due Time:</td>
                    <td style="color: #555;">{{ $activity->due_time }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #333;">Points:</td>
                    <td style="color: #555;">{{ $activity->points }}</td>
                </tr>
            </table>

            <!-- ✅ Action Button with Link Icon -->
            <div style="margin-top: 30px; text-align: center;">
                <a href="{{ url('/student/class/i/' . $class->id) }}"
                   style="display: inline-block; padding: 12px 20px; background-color: #1a73e8; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold;">
                    🔗 View Activity
                </a>
            </div>

            <p style="margin-top: 30px; color: #888; font-size: 13px;">
                You are receiving this email because you are enrolled in <strong>{{ $class->name ?? 'a class' }}</strong> and a new activity has been posted by your instructor.
            </p>
        </div>
        <div style="background-color: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #999;">
            &copy; {{ date('Y') }} PyScore. All rights reserved.
        </div>
    </div>
</body>
</html>
