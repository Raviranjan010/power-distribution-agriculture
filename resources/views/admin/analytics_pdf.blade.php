<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Analytics Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 11pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        /* Government Letterhead */
        .letterhead {
            text-align: center;
            border-bottom: 3px double #1e3a1e;
            padding-bottom: 12px;
            margin-bottom: 25px;
        }
        .letterhead .logo-placeholder {
            font-size: 14pt;
            font-weight: bold;
            color: #1e3a1e;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2px;
        }
        .letterhead .sub-dept {
            font-size: 10pt;
            color: #b89040;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .letterhead .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #111;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 9.5pt;
            background: #f7f9f6;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 5px;
        }
        .meta-table td {
            padding: 4px 10px;
        }
        .meta-label {
            font-weight: bold;
            color: #4a5568;
        }
        .meta-value {
            color: #1a202c;
        }
        
        h2 {
            font-size: 13pt;
            color: #1e3a1e;
            border-left: 4px solid #b89040;
            padding-left: 8px;
            margin-top: 25px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #1e3a1e;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            font-size: 9.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9.5pt;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f7f9f6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        
        /* Metric Box Grid */
        .grid-box {
            display: inline-block;
            width: 23%;
            margin-right: 1.5%;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-top: 3px solid #1e3a1e;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
        }
        .grid-box:last-child {
            margin-right: 0;
        }
        .grid-box .val {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a1e;
            margin: 5px 0;
        }
        .grid-box .lbl {
            font-size: 8pt;
            color: #475569;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .footer {
            margin-top: 50px;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            text-align: center;
            font-size: 8pt;
            color: #64748b;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Letterhead -->
    <div class="letterhead">
        <div class="logo-placeholder">Ministry of Agriculture & Power</div>
        <div class="sub-dept">Government of Punjab · Department of Power Distribution</div>
        <div class="report-title">Executive Analytics Report</div>
    </div>

    <!-- Metadata Panel -->
    <table class="meta-table">
        <tr>
            <td class="meta-label" width="15%">Report ID:</td>
            <td class="meta-value" width="35%">REP-{{ now()->format('YmdHis') }}</td>
            <td class="meta-label" width="20%">Generated At:</td>
            <td class="meta-value" width="30%">{{ now()->format('d M Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Audited By:</td>
            <td class="meta-value">{{ $adminName }} (Administrator)</td>
            <td class="meta-label">System Status:</td>
            <td class="meta-value" style="color: #1e3a1e; font-weight: bold;">CERTIFIED OPERATIONAL</td>
        </tr>
    </table>

    <!-- Key Aggregate Metrics -->
    <h2>1. Executive Summary</h2>
    <div style="margin-bottom: 30px;">
        <div class="grid-box">
            <div class="lbl">Total Farmers</div>
            <div class="val">{{ number_format($totalFarmers) }}</div>
        </div>
        <div class="grid-box">
            <div class="lbl">Active Connections</div>
            <div class="val">{{ number_format($totalActiveConnections) }}</div>
        </div>
        <div class="grid-box">
            <div class="lbl">Revenue This Month</div>
            <div class="val">₹{{ number_format($totalRevenueThisMonth, 2) }}</div>
        </div>
        <div class="grid-box">
            <div class="lbl">Pending Complaints</div>
            <div class="val">{{ number_format($pendingComplaints) }}</div>
        </div>
    </div>

    <!-- Revenue Per Zone Heatmap / Table -->
    <h2>2. Revenue Per Zone Heatmap</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th width="50%">Zone Name</th>
                <th width="50%" class="text-right">Revenue Collection (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenuePerZone as $rz)
                <tr>
                    <td class="font-bold">{{ $rz['zone'] }}</td>
                    <td class="text-right font-bold text-emerald-700">₹{{ number_format($rz['revenue'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Overdue Bill Aging Buckets -->
    <h2>3. Overdue Bill Aging Buckets</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Aging Bracket (Days)</th>
                <th class="text-right">Outstanding Amount (₹)</th>
                <th class="text-center">Risk Level</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>0–30 Days Overdue</td>
                <td class="text-right">₹{{ number_format($agingBuckets['0_30'], 2) }}</td>
                <td class="text-center font-bold" style="color: #e28743;">LOW RISK</td>
            </tr>
            <tr>
                <td>30–60 Days Overdue</td>
                <td class="text-right">₹{{ number_format($agingBuckets['30_60'], 2) }}</td>
                <td class="text-center font-bold" style="color: #eab308;">MEDIUM RISK</td>
            </tr>
            <tr>
                <td>60–90 Days Overdue</td>
                <td class="text-right">₹{{ number_format($agingBuckets['60_90'], 2) }}</td>
                <td class="text-center font-bold" style="color: #ea580c;">HIGH RISK</td>
            </tr>
            <tr>
                <td>90+ Days Overdue</td>
                <td class="text-right">₹{{ number_format($agingBuckets['90_plus'], 2) }}</td>
                <td class="text-center font-bold" style="color: #e11d48;">CRITICAL RISK</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Lineman Performance Tracker -->
    <h2>4. Lineman Performance Performance</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Lineman Name</th>
                <th class="text-center">Readings Recorded (This Month)</th>
                <th class="text-center">Verifications Completed Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($linemanPerformance as $lm)
                <tr>
                    <td class="font-bold">{{ $lm['name'] }}</td>
                    <td class="text-center">{{ $lm['readings_this_month'] }}</td>
                    <td class="text-center font-bold text-indigo-700">{{ $lm['verification_rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Complaint Resolution Trend -->
    <h2>5. Complaint Resolution Trend</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Resolution Month</th>
                <th class="text-right">Average Complaint Resolution Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resolutionTimeTrend as $trend)
                <tr>
                    <td class="font-bold">{{ $trend['label'] }}</td>
                    <td class="text-right font-bold text-amber-700">{{ $trend['avg_hours'] }} Hours</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Certification Signature -->
    <div style="margin-top: 40px; float: right; width: 220px; text-align: center;">
        <p style="margin-bottom: 45px; font-size: 9.5pt; color: #64748b;">AUTHORIZED SYSTEM SIGN-OFF</p>
        <div style="border-bottom: 1px solid #333; margin-bottom: 5px;"></div>
        <p class="font-bold" style="font-size: 9.5pt; margin: 0;">{{ $adminName }}</p>
        <p style="font-size: 8pt; color: #64748b; margin: 0;">Lead Grid Administrator</p>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>CONFIDENTIAL · FOR INTERNAL GOVERNMENT REVIEW ONLY · POWER DISTRIBUTION PUNJAB</p>
        <p>© 2026 Ministry of Agriculture & Power, Govt of Punjab. All rights reserved.</p>
    </div>

</body>
</html>
