<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Complete</title>
    <style>
        body { font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #f5f5f5; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .footer { margin-top: 32px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Service Complete</h1>
    <div class="card">
        <p style="white-space: pre-wrap; margin: 0;">{{ $rendered_body }}</p>
    </div>
    <div class="footer">
        Thanks,<br>
        {{ config('app.name') }}
    </div>
</body>
</html>
