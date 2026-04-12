<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
    <style>
        body { font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #f5f5f5; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .amount { font-size: 24px; font-weight: bold; color: #dc2626; }
        .footer { margin-top: 32px; color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <h1>Payment Reminder</h1>

    <p>Hi {{ $invoice->customer->first_name }},</p>

    <p>This is a friendly reminder that the following invoice is still outstanding:</p>

    <div class="card">
        <p><strong>Invoice:</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Amount Due:</strong> <span class="amount">${{ number_format($invoice->balance_due, 2) }}</span></p>
        @if ($invoice->due_at)
            <p><strong>Due Date:</strong> {{ $invoice->due_at->format('F j, Y') }}</p>
        @endif
    </div>

    <p>Please arrange payment at your earliest convenience. If you have already paid, please disregard this notice.</p>

    <div class="footer">
        Thanks,<br>
        {{ config('app.name') }}
    </div>
</body>
</html>
