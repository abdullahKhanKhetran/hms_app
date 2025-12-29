<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Patient History Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2, h3 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .header { text-align: center; margin-bottom: 20px; }
        .info p { margin: 3px 0; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>

<div class="header">
    <h2>Hospital Management System</h2>
    <h3>Patient Medical History Report</h3>
</div>

<div class="info">
    <p><strong>Patient Name:</strong> {{ $appointment->patient->name }}</p>
    <p><strong>Doctor:</strong> Dr. {{ $appointment->doctor->name }}</p>
    <p><strong>Specialization:</strong> {{ $appointment->doctor->doctorProfile->specialization ?? 'N/A' }}</p>
    <p><strong>Generated On:</strong> {{ now()->format('d M Y') }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Remarks</th>
            <th>Fee</th>
            <th>Discount %</th>
            <th>Final Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($previousAppointments as $index => $apt)
            @php
                $fee = $apt->doctor->doctorProfile->fee ?? 0;
                $discount = $apt->discount ?? 0;
                $discountPercent = $fee > 0 ? round(($discount / $fee) * 100, 2) : 0;
                $final = $apt->final_amount ?? ($fee - $discount);
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d M Y') }}</td>
                <td>{{ $apt->doctor_remarks ?? '—' }}</td>
                <td>{{ number_format($fee, 2) }}</td>
                <td>{{ $discountPercent }}%</td>
                <td>{{ number_format($final, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;">No previous appointments found</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    <p>Doctor Signature: ____________________</p>
</div>

</body>
</html>
