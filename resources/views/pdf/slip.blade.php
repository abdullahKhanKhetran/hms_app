<!DOCTYPE html>
<html>
<head>
    <title>Appointment Slip</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #0061f2; }
        .info-box { background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .footer { margin-top: 40px; font-size: 12px; text-align: center; color: #777; }
        .status-approved { color: green; font-weight: bold; text-transform: uppercase; border: 2px solid green; padding: 5px 10px; display: inline-block; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">HMS Hospital</div>
        <p>Official Appointment Slip</p>
    </div>

    <div class="info-box">
        <h3>Appointment ID: #{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</h3>
        
        @if($appointment->status == 'approved')
            <div style="text-align: right;">
                <span class="status-approved">Confirmed</span>
            </div>
        @endif
    </div>

    <table>
        <tr>
            <th>Patient Name:</th>
            <td>{{ $appointment->patient->name }}</td>
        </tr>
        <tr>
            <th>Doctor:</th>
            <td>Dr. {{ $appointment->doctor->name }}</td>
        </tr>
        <tr>
            <th>Department:</th>
            <td>{{ $appointment->doctor->doctorProfile->specialization ?? 'General' }}</td>
        </tr>
        <tr>
            <th>Date:</th>
            <td>{{ $appointment->appointment_date }}</td>
        </tr>
        <tr>
            <th>Time Slot:</th>
            <td>{{ $appointment->doctor->doctorProfile->start_time }} - {{ $appointment->doctor->doctorProfile->end_time }}</td>
        </tr>

        <tr>
    <th>Token Number:</th>
    <td style="font-size: 18px; font-weight: bold;">
        {{ $appointment->queueTicket ? str_pad($appointment->queueTicket->token_number, 3, '0', STR_PAD_LEFT) : 'Pending' }}
    </td>
</tr>
    </table>

    <div class="footer">
        <p>Please arrive 15 minutes before your scheduled time.</p>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>