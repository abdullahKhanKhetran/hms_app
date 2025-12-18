<?php

namespace App\Http\Controllers;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $appointments = Appointment::where('doctor_id', Auth::id())
                        ->with('patient')
                        ->where('appointment_date', '>=', now()->toDateString())
                        ->orderBy('appointment_date', 'asc')
                        ->get();

        return view('doctor.dashboard', compact('appointments'));
    }
    
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        if ($appointment->doctor_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:approved,cancelled,completed',
            'discount' => 'nullable|numeric|min:0',
            'doctor_remarks' => 'nullable|string'
        ]);

        if ($request->has('discount')) {
            $appointment->discount = $request->discount;
        }
        
        if ($request->has('doctor_remarks')) {
            $appointment->doctor_remarks = $request->doctor_remarks;
        }

        if ($request->status == 'approved' && $appointment->status != 'approved') {
            $fee = $appointment->doctor->doctorProfile->fee ?? 0;
            $appointment->final_amount = $fee - $appointment->discount;
            
            $todayTokens = QueueTicket::whereHas('appointment', function($q) use ($appointment) {
                $q->where('doctor_id', $appointment->doctor_id)
                  ->where('appointment_date', $appointment->appointment_date);
            })->count();

            QueueTicket::create([
                'appointment_id' => $appointment->id,
                'token_number' => $todayTokens + 1,
                'status' => 'waiting'
            ]);
        }

        if ($request->status == 'completed') {
            $ticket = QueueTicket::where('appointment_id', $appointment->id)->first();
            if ($ticket) {
                $ticket->update(['status' => 'done']);
            }
            
            if (!$appointment->final_amount) {
                $fee = $appointment->doctor->doctorProfile->fee ?? 0;
                $appointment->final_amount = $fee - $appointment->discount;
            }
        }

        $appointment->status = $request->status;
        $appointment->save();

        return back()->with('success', 'Status updated successfully!');
    }
    
    /**
     * Preview appointment and patient history (AJAX)
     */
    public function previewAppointment($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.doctorProfile', 'queueTicket'])
            ->findOrFail($id);
        
        // Check authorization
        if ($appointment->doctor_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get patient's previous appointments with this doctor
        $previousAppointments = Appointment::where('patient_id', $appointment->patient_id)
            ->where('doctor_id', Auth::id())
            ->where('id', '!=', $id)
            ->where('status', 'completed')
            ->with('doctor.doctorProfile')
            ->orderBy('appointment_date', 'desc')
            ->take(10)
            ->get();
        
        // Format current appointment data
        $currentData = [
            'patient_name' => $appointment->patient->name,
            'date' => \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y'),
            'status_badge' => $this->getStatusBadge($appointment->status),
            'fee' => number_format($appointment->doctor->doctorProfile->fee ?? 0, 2),
            'discount' => number_format($appointment->discount ?? 0, 2),
            'final_amount' => number_format($appointment->final_amount ?? 
                (($appointment->doctor->doctorProfile->fee ?? 0) - ($appointment->discount ?? 0)), 2),
            'remarks' => $appointment->doctor_remarks
        ];
        
        // Format history data
        $historyData = $previousAppointments->map(function($apt) {
            return [
                'date' => \Carbon\Carbon::parse($apt->appointment_date)->format('d M Y'),
                'doctor' => 'Dr. ' . $apt->doctor->name,
                'department' => $apt->doctor->doctorProfile->specialization ?? 'N/A',
                'remarks' => \Illuminate\Support\Str::limit($apt->doctor_remarks ?? '', 100)
            ];
        });
        
        return response()->json([
            'current' => $currentData,
            'history' => $historyData
        ]);
    }
    
    /**
     * Download PDF for appointment
     */
    public function downloadAppointmentPdf($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.doctorProfile', 'queueTicket'])
            ->findOrFail($id);
        
        // Check authorization
        if ($appointment->doctor_id != Auth::id()) {
            abort(403);
        }
        
        // Get patient's previous appointments
        $previousAppointments = Appointment::where('patient_id', $appointment->patient_id)
            ->where('doctor_id', Auth::id())
            ->where('id', '!=', $id)
            ->where('status', 'completed')
            ->with('doctor.doctorProfile')
            ->orderBy('appointment_date', 'desc')
            ->take(10)
            ->get();
        
        $pdf = Pdf::loadView('pdf.doctor_slip', compact('appointment', 'previousAppointments'));
        
        return $pdf->download('patient-history-' . $appointment->patient->name . '-' . $id . '.pdf');
    }
    
    /**
     * Helper to generate status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'completed' => '<span class="badge bg-secondary">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ];
        
        return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
    }
}