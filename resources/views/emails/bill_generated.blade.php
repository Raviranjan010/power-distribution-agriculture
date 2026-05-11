<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: #15803d; padding: 24px 30px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 18px; margin: 0 0 4px; }
        .header p { color: #bbf7d0; font-size: 12px; margin: 0; }
        .body { padding: 30px; color: #1a1a1a; line-height: 1.6; }
        .body p { margin: 0 0 12px; font-size: 14px; }
        .highlight-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .highlight-box table { width: 100%; border-collapse: collapse; }
        .highlight-box td { padding: 4px 0; font-size: 13px; }
        .highlight-box td:first-child { color: #666; }
        .highlight-box td:last-child { font-weight: bold; text-align: right; }
        .amount { font-size: 28px; font-weight: bold; color: #15803d; text-align: center; margin: 16px 0; }
        .btn { display: inline-block; background: #15803d; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 11px; margin: 0 0 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⚡ Punjab State Power Corporation</h1>
        <p>Agricultural Electricity Distribution</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $bill->connection->consumer->name }}</strong>,</p>
        <p>Your electricity bill for <strong>{{ \Carbon\Carbon::create($bill->billing_year, $bill->billing_month)->format('F Y') }}</strong> has been generated.</p>

        <div class="highlight-box">
            <table>
                <tr><td>Bill Number</td><td>{{ $bill->bill_number }}</td></tr>
                <tr><td>Connection</td><td>{{ $bill->connection->connection_number }}</td></tr>
                <tr><td>Units Consumed</td><td>{{ number_format($bill->units_consumed) }} kWh</td></tr>
                <tr><td>Energy Charges</td><td>₹{{ number_format($bill->energy_charges, 2) }}</td></tr>
                <tr><td>Fixed Charges</td><td>₹{{ number_format($bill->fixed_charges, 2) }}</td></tr>
                <tr><td>Taxes</td><td>₹{{ number_format($bill->taxes, 2) }}</td></tr>
                @if($bill->subsidy_amount > 0)
                <tr><td>Subsidy Deduction</td><td style="color: #15803d;">− ₹{{ number_format($bill->subsidy_amount, 2) }}</td></tr>
                @endif
            </table>
        </div>

        <div class="amount">₹{{ number_format($bill->net_payable, 2) }}</div>

        <p style="text-align: center; font-size: 13px; color: #666;">
            Due Date: <strong style="color: #dc2626;">{{ $bill->due_date->format('d F Y') }}</strong>
        </p>

        <p style="text-align: center; margin: 24px 0;">
            <a href="{{ url('/farmer/bills') }}" class="btn">View & Pay Bill</a>
        </p>

        <p style="font-size: 12px; color: #888;">Late payment after the due date may attract a surcharge of 2% per month. Please ensure timely payment to avoid disconnection.</p>
    </div>
    <div class="footer">
        <p>📞 Helpline: 1800-180-1512 (Toll Free)</p>
        <p>Punjab State Power Corporation Limited · Patiala, Punjab</p>
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>
</body>
</html>
