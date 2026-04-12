<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Confirmation</title>
    <style>
        body { font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #f5f5f5; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .footer { margin-top: 32px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Job Confirmed</h1>

    @if (!empty($rendered_body))
        <div class="card">
            <p style="white-space: pre-wrap; margin: 0;">{{ $rendered_body }}</p>
        </div>
    @else
        <p>Hi {{ $job->customer->first_name }},</p>

        <p>Your service appointment has been confirmed. Here are the details:</p>

        <div class="card">
            <strong>{{ $job->title }}</strong>

            @if ($job->scheduled_at)
                <p><strong>Scheduled:</strong> {{ $job->scheduled_at->format('l, F j, Y \a\t g:i A') }}</p>
            @endif

            @if ($job->description)
                <p>{{ $job->description }}</p>
            @endif
        </div>

        <p>If you have any questions, please don't hesitate to contact us.</p>
    @endif

    <div class="footer">
        Thanks,<br>
        {{ config('app.name') }}
    </div>
</body>
</html>
