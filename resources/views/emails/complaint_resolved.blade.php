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
        <h1>⚡ Punjab State Power Corporation</h1>
        <p>Agricultural Electricity Distribution</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $complaint->consumer->name }}</strong>,</p>
        <p>Your complaint has been <span class="badge">✓ RESOLVED</span></p>

        <div class="highlight-box">
            <table>
                <tr><td>Grievance Number</td><td>{{ $complaint->grv_number }}</td></tr>
                <tr><td>Complaint Type</td><td>{{ ucwords(str_replace('_', ' ', $complaint->complaint_type)) }}</td></tr>
                <tr><td>Filed On</td><td>{{ $complaint->filed_at->format('d F Y') }}</td></tr>
                <tr><td>Resolved On</td><td>{{ $complaint->resolved_at ? $complaint->resolved_at->format('d F Y') : now()->format('d F Y') }}</td></tr>
                @if($complaint->resolution_remarks)
                <tr><td>Resolution Remarks</td><td>{{ $complaint->resolution_remarks }}</td></tr>
                @endif
            </table>
        </div>

        <p>If you are not satisfied with the resolution, you may file a new complaint or contact the SDO office directly.</p>

        <p style="text-align: center; margin: 24px 0;">
            <a href="{{ url('/farmer/complaints') }}" class="btn">View My Complaints</a>
        </p>

        <p style="font-size: 12px; color: #888;">We value your feedback. If you feel the issue persists, please don't hesitate to reach out.</p>
    </div>
    <div class="footer">
        <p>📞 Helpline: 1800-180-1512 (Toll Free)</p>
        <p>Punjab State Power Corporation Limited · Patiala, Punjab</p>
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>
</body>
</html>
