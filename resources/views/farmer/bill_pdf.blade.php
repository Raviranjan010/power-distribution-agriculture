<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Electricity Bill — {{ $bill->bill_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }

        .page { padding: 30px 40px; }

        /* Header / Letterhead */
        .header { text-align: center; border-bottom: 3px solid #15803d; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: bold; color: #15803d; margin-bottom: 2px; }
        .header h2 { font-size: 13px; font-weight: normal; color: #333; margin-bottom: 2px; }
        .header p { font-size: 10px; color: #666; }
        .logo-placeholder { width: 60px; height: 60px; border: 2px solid #15803d; border-radius: 50%; margin: 0 auto 8px; display: flex; align-items: center; justify-content: center; }
        .logo-placeholder span { font-size: 22px; color: #15803d; font-weight: bold; }

        /* Bill Meta */
        .bill-meta { display: table; width: 100%; margin-bottom: 20px; }
        .bill-meta .left, .bill-meta .right { display: table-cell; width: 50%; vertical-align: top; }
        .bill-meta .right { text-align: right; }
        .meta-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #888; font-weight: bold; }
        .meta-value { font-size: 12px; font-weight: bold; color: #1a1a1a; margin-bottom: 6px; }

        /* Status Badge */
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-overdue { background: #fee2e2; color: #991b1b; }

        /* Consumer Info */
        .consumer-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; }
        .consumer-box h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #15803d; font-weight: bold; margin-bottom: 8px; }

        /* Charges Table */
        .charges-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .charges-table th { background: #15803d; color: white; padding: 8px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .charges-table th:last-child { text-align: right; }
        .charges-table td { padding: 8px 12px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .charges-table td:last-child { text-align: right; font-weight: bold; }
        .charges-table tr:nth-child(even) { background: #f8fafc; }
        .charges-table .subtotal td { border-top: 2px solid #15803d; font-weight: bold; font-size: 12px; background: #f0fdf4; }
        .charges-table .deduction td { color: #15803d; }
        .charges-table .total td { background: #15803d; color: white; font-size: 14px; font-weight: bold; border: none; }

        /* QR & Payment */
        .payment-section { display: table; width: 100%; margin-bottom: 20px; }
        .payment-section .qr-col { display: table-cell; width: 120px; vertical-align: top; }
        .payment-section .info-col { display: table-cell; vertical-align: top; padding-left: 16px; }
        .qr-placeholder { width: 100px; height: 100px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
        .qr-placeholder span { font-size: 9px; color: #999; text-align: center; }

        .payment-instructions { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; font-size: 10px; color: #92400e; }
        .payment-instructions h4 { font-size: 11px; font-weight: bold; margin-bottom: 4px; }

        /* Footer */
        .footer { border-top: 2px solid #15803d; padding-top: 12px; margin-top: 20px; text-align: center; }
        .footer p { font-size: 9px; color: #888; }
        .footer .helpline { font-size: 11px; color: #15803d; font-weight: bold; margin-bottom: 4px; }
        .footer .disclaimer { font-style: italic; font-size: 8px; color: #aaa; margin-top: 6px; }

        .two-col { display: table; width: 100%; }
        .two-col .col { display: table-cell; width: 50%; vertical-align: top; }
        .two-col .col:last-child { padding-left: 15px; }
    </style>
</head>
<body>
<div class="page">
    <!-- Letterhead -->
    <div class="header">
        <div class="logo-placeholder"><span>⚡</span></div>
        <h1>Punjab State Power Corporation Limited</h1>
        <h2>Ministry of Power — Government of India</h2>
        <p>Agricultural Electricity Distribution Division</p>
    </div>

    <!-- Bill Meta -->
    <div class="bill-meta">
        <div class="left">
            <p class="meta-label">Bill Number</p>
            <p class="meta-value">{{ $bill->bill_number }}</p>
            <p class="meta-label">Billing Period</p>
            <p class="meta-value">{{ \Carbon\Carbon::create($bill->billing_year, $bill->billing_month)->format('F Y') }}</p>
            <p class="meta-label">Issue Date</p>
            <p class="meta-value">{{ $bill->created_at->format('d M Y') }}</p>
        </div>
        <div class="right">
            <p class="meta-label">Due Date</p>
            <p class="meta-value" style="color: #dc2626;">{{ $bill->due_date->format('d M Y') }}</p>
            <p class="meta-label">Status</p>
            <p>
                <span class="status-badge status-{{ $bill->status }}">{{ ucwords($bill->status) }}</span>
            </p>
        </div>
    </div>

    <!-- Consumer Info -->
    <div class="consumer-box">
        <h3>Consumer Details</h3>
        <div class="two-col">
            <div class="col">
                <p class="meta-label">Consumer Name</p>
                <p class="meta-value">{{ $bill->connection->consumer->name }}</p>
                <p class="meta-label">Farmer ID</p>
                <p class="meta-value">{{ $bill->connection->consumer->farmer_id_number ?? '—' }}</p>
                <p class="meta-label">Address</p>
                <p class="meta-value">{{ $bill->connection->consumer->village ?? '' }}, {{ $bill->connection->consumer->district ?? '' }}, {{ $bill->connection->consumer->state ?? 'Punjab' }}</p>
            </div>
            <div class="col">
                <p class="meta-label">Connection Number</p>
                <p class="meta-value">{{ $bill->connection->connection_number }}</p>
                <p class="meta-label">Connection Type</p>
                <p class="meta-value">{{ ucwords(str_replace('_', ' ', $bill->connection->connection_type)) }}</p>
                <p class="meta-label">Sanctioned Load</p>
                <p class="meta-value">{{ $bill->connection->sanctioned_load_kw }} kW</p>
                <p class="meta-label">Tariff Category</p>
                <p class="meta-value">{{ $bill->connection->tariffCategory->name ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Charges Breakdown -->
    <table class="charges-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Units Consumed</td>
                <td>{{ number_format($bill->units_consumed) }} kWh</td>
            </tr>
            <tr>
                <td>Rate Per Unit</td>
                <td>₹{{ number_format($bill->rate_per_unit, 2) }} / kWh</td>
            </tr>
            <tr>
                <td>Energy Charges ({{ number_format($bill->units_consumed) }} × ₹{{ number_format($bill->rate_per_unit, 2) }})</td>
                <td>₹{{ number_format($bill->energy_charges, 2) }}</td>
            </tr>
            <tr>
                <td>Fixed Charges ({{ $bill->connection->sanctioned_load_kw }} kW)</td>
                <td>₹{{ number_format($bill->fixed_charges, 2) }}</td>
            </tr>
            <tr class="subtotal">
                <td>Subtotal</td>
                <td>₹{{ number_format($bill->energy_charges + $bill->fixed_charges, 2) }}</td>
            </tr>
            <tr>
                <td>Taxes & Surcharges</td>
                <td>₹{{ number_format($bill->taxes, 2) }}</td>
            </tr>
            @if($bill->subsidy_amount > 0)
                <tr class="deduction" style="font-weight: bold; background-color: #f0fdf4;">
                    <td>Government Subsidy Deduction (Total)</td>
                    <td>− ₹{{ number_format($bill->subsidy_amount, 2) }}</td>
                </tr>
                @php
                    $appliedSubsidies = \App\Models\ConsumerSubsidy::where('consumer_id', $bill->connection->consumer_id)
                        ->where('status', 'approved')
                        ->whereHas('scheme', fn($q) => $q->where('is_active', true))
                        ->with('scheme')
                        ->get();
                @endphp
                @foreach($appliedSubsidies as $cs)
                    @php
                        $scheme = $cs->scheme;
                        $coveredUnits = min($bill->units_consumed, $scheme->max_units_covered);
                        $schemeSubsidy = $coveredUnits * $bill->rate_per_unit * ($scheme->discount_percentage / 100);
                    @endphp
                    @if($schemeSubsidy > 0)
                        <tr class="deduction">
                            <td style="font-size: 10px; padding-left: 20px; color: #166534; font-style: italic;">
                                ↳ Applied Scheme: {{ $scheme->scheme_name }} ({{ number_format($scheme->discount_percentage, 0) }}% off up to {{ number_format($scheme->max_units_covered, 0) }} kWh)
                            </td>
                            <td style="font-size: 10px; color: #166534; font-style: italic;">− ₹{{ number_format($schemeSubsidy, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
            <tr class="total">
                <td>NET PAYABLE AMOUNT</td>
                <td>₹{{ number_format($bill->net_payable, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- QR Code & Payment Instructions -->
    <div class="payment-section">
        <div class="qr-col">
            <div class="qr-placeholder">
                <span>QR Code<br>for Payment</span>
            </div>
        </div>
        <div class="info-col">
            <div class="payment-instructions">
                <h4>Payment Instructions</h4>
                <p>1. Pay online through the farmer portal at your dashboard.</p>
                <p>2. Pay via UPI by scanning the QR code on the left.</p>
                <p>3. Pay at any designated Punjab State Power Corporation office.</p>
                <p>4. Cheque / DD payable to "PSPCL Agriculture Division".</p>
                <p style="margin-top: 6px; font-weight: bold;">Late payment after due date may attract a surcharge of 2% per month.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p class="helpline">📞 Helpline: 1800-180-1512 (Toll Free) | ✉ support@punjabpower.gov.in</p>
        <p>Punjab State Power Corporation Limited · Patiala, Punjab — 147001</p>
        <p>This is a computer-generated bill and does not require a signature.</p>
        <p class="disclaimer">In case of any dispute, the consumer may contact the nearest PSPCL office or file a grievance through the portal.</p>
    </div>
</div>
</body>
</html>
