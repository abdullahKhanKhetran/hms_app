<!DOCTYPE html>
<html>
<head>
    <title>Appointment Slip</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #0061f2; }
        .info-box { background: #f8f9fa; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        .footer { margin-top: 40px; font-size: 12px; text-align: center; color: #777; }
        .status-approved { color: green; font-weight: bold; text-transform: uppercase; border: 2px solid green; padding: 5px 10px; display: inline-block; }
        .billing-section { background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .billing-section h3 { margin-top: 0; color: #856404; }
        .total-row { font-size: 18px; font-weight: bold; border-top: 2px solid #333; }
        .remarks-box { background: #e7f3ff; padding: 15px; margin: 20px 0; border-left: 4px solid #0061f2; }
        .history-section { margin-top: 30px; page-break-before: auto; }
        .history-table { font-size: 12px; }
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

    <!-- Billing Section -->
    <div class="billing-section">
        <h3>Billing Details</h3>
        <table>
            <tr>
                <th>Doctor Fee:</th>
                <td>Rs. {{ number_format($appointment->doctor->doctorProfile->fee ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Discount:</th>
                <td style="color: green;">- Rs. {{ number_format($appointment->discount ?? 0, 2) }}</td>
            </tr>
            <tr class="total-row">
                <th>Total Amount:</th>
                <td>Rs. {{ number_format($appointment->final_amount ?? ($appointment->doctor->doctorProfile->fee - $appointment->discount), 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Doctor's Remarks -->
    @if($appointment->doctor_remarks)
        <div class="remarks-box">
            <h4 style="margin-top: 0;">Doctor's Remarks / Prescription</h4>
            <p style="white-space: pre-wrap;">{{ $appointment->doctor_remarks }}</p>
        </div>
    @endif

    <!-- Patient History Section -->
    @if(isset($pastAppointments) && $pastAppointments->count() > 0)
        <div class="history-section">
            <h3>Patient Visit History</h3>
            <table class="history-table">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Department</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pastAppointments as $past)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($past->appointment_date)->format('d M Y') }}</td>
                            <td>Dr. {{ $past->doctor->name }}</td>
                            <td>{{ $past->doctor->doctorProfile->specialization ?? 'N/A' }}</td>
                            <td>{{ Str::limit($past->doctor_remarks ?? 'No remarks', 50) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Please arrive 15 minutes before your scheduled time.</p>
        <p>For any queries, contact: support@hmshospital.com | +92-XXX-XXXXXXX</p>
        <p>Generated on {{ date('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>