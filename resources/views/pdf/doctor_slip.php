<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctor Slip</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .section {
            margin-bottom: 15px;
        }

        .label {
            font-weight: bold;
            width: 140px;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table, th, td {
            border: 1px solid #000;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>Hospital Management System</h2>
        <p>Doctor Appointment Slip</p>
    </div>

    {{-- Doctor Info --}}
    <div class="section">
        <div><span class="label">Doctor:</span> {{ $appointment->doctor->name }}</div>
        <div><span class="label">Specialization:</span> {{ $appointment->doctor->doctorProfile->specialization ?? 'N/A' }}</div>
        <div><span class="label">Consultation Fee:</span> {{ $appointment->doctor->doctorProfile->fee ?? 0 }}</div>
    </div>

    {{-- Patient Info --}}
    <div class="section">
        <div><span class="label">Patient:</span> {{ $appointment->patient->name }}</div>
        <div><span class="label">Appointment Date:</span> {{ $appointment->appointment_date }}</div>
        <div><span class="label">Status:</span> {{ ucfirst($appointment->status) }}</div>
        <div><span class="label">Token No:</span> {{ optional($appointment->queueTicket)->token_number ?? 'N/A' }}</div>
    </div>

    {{-- Fee Breakdown --}}
    @php
        $originalFee = $appointment->doctor->doctorProfile->fee ?? 0;
        $discount = $appointment->discount ?? 0;
        $finalAmount = $appointment->final_amount ?? $originalFee;

        $discountPercent = $originalFee > 0
            ? round(($discount / $originalFee) * 100, 2)
            : 0;
    @endphp

    <table>
        <tr>
            <th>Description</th>
            <th>Amount</th>
        </tr>
        <tr>
            <td>Consultation Fee</td>
            <td>{{ $originalFee }}</td>
        </tr>
        <tr>
            <td>Discount ({{ $discountPercent }}%)</td>
            <td>- {{ $discount }}</td>
        </tr>
        <tr>
            <td class="total">Final Payable</td>
            <td class="total">{{ $finalAmount }}</td>
        </tr>
    </table>

    {{-- Doctor Remarks --}}
    @if($appointment->doctor_remarks)
        <div class="section">
            <span class="label">Doctor Remarks:</span>
            <p>{{ $appointment->doctor_remarks }}</p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="signature">
            Doctor Signature
        </div>
        <div class="signature">
            Patient Signature
        </div>
    </div>

</body>
</html>
