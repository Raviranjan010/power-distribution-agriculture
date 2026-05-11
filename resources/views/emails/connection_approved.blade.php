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
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .btn { display: inline-block; background: #15803d; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer p { color: #9ca3af; font-size: 11px; margin: 0 0 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⚡ AgriPower — Ministry of Power</h1>
        <p>Agricultural Electricity Distribution</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $conn->consumer->name }}</strong>,</p>
        <p>We are pleased to inform you that your electricity connection request has been <span class="badge">✓ APPROVED</span></p>

        <div class="highlight-box">
            <table>
                <tr><td>Connection Number</td><td>{{ $conn->connection_number }}</td></tr>
                <tr><td>Connection Type</td><td>{{ ucwords(str_replace('_', ' ', $conn->connection_type)) }}</td></tr>
                <tr><td>Field / Location</td><td>{{ $conn->field_name }}</td></tr>
                <tr><td>Sanctioned Load</td><td>{{ $conn->sanctioned_load_kw }} kW</td></tr>
                <tr><td>Meter Number</td><td>{{ $conn->meter_number }}</td></tr>
                <tr><td>Tariff Category</td><td>{{ $conn->tariffCategory->name ?? '—' }}</td></tr>
                <tr><td>Installation Date</td><td>{{ $conn->installation_date ? \Carbon\Carbon::parse($conn->installation_date)->format('d F Y') : '—' }}</td></tr>
            </table>
        </div>

        <p>Your connection is now active. A lineman will be assigned to take your first meter reading during the next billing cycle.</p>

        <p style="text-align: center; margin: 24px 0;">
            <a href="{{ url('/farmer/connections') }}" class="btn">View My Connections</a>
        </p>

        <p style="font-size: 12px; color: #888;">If you have any questions about your new connection, please contact your nearest AgriPower office or call our helpline.</p>
    </div>
    <div class="footer">
        <p>📞 Helpline: 1800-111-5555 (Toll Free)</p>
        <p>Ministry of Power · Agriculture Distribution Division</p>
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>
</body>
</html>
