<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    /**
     * Doctor Dashboard
     */
    public function dashboard()
    {
        $appointments = Appointment::with('patient')
            ->where('doctor_id', Auth::id())
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->get();

        return view('doctor.dashboard', compact('appointments'));
    }

    /**
     * Update appointment status, discount & remarks
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::with('doctor.doctorProfile')->findOrFail($id);
        $this->authorizeDoctor($appointment);

        $request->validate([
            'status' => 'required|in:approved,cancelled,completed',
            'discount' => 'nullable|numeric|min:0',
            'doctor_remarks' => 'nullable|string'
        ]);

        $appointment->discount = $request->discount ?? $appointment->discount ?? 0;
        $appointment->doctor_remarks = $request->doctor_remarks ?? $appointment->doctor_remarks;

        if ($request->status === 'approved' && $appointment->status !== 'approved') {
            $this->approveAppointment($appointment);
        }

        if ($request->status === 'completed') {
            $this->completeAppointment($appointment);
        }

        $appointment->status = $request->status;
        $appointment->save();

        return back()->with('success', 'Appointment updated successfully!');
    }

    /**
     * Preview appointment & patient history (AJAX)
     */
    public function previewAppointment($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.doctorProfile', 'queueTicket'])
            ->findOrFail($id);

        $this->authorizeDoctor($appointment);

        $history = $this->getPatientHistory($appointment, 10);

        $fee = $appointment->doctor->doctorProfile->fee ?? 0;
        $discount = $appointment->discount ?? 0;

        return response()->json([
            'current' => [
                'patient_name' => $appointment->patient->name,
                'date' => Carbon::parse($appointment->appointment_date)->format('d M Y'),
                'status_badge' => $this->getStatusBadge($appointment->status),
                'fee' => number_format($fee, 2),
                'discount' => number_format($discount, 2),
                'final_amount' => number_format(
                    $appointment->final_amount ?? ($fee - $discount),
                    2
                ),
                'remarks' => $appointment->doctor_remarks,
                'token' => optional($appointment->queueTicket)->token_number
            ],
            'history' => $history->map(fn ($apt) => [
                'date' => Carbon::parse($apt->appointment_date)->format('d M Y'),
                'remarks' => Str::limit($apt->doctor_remarks ?? '', 100),
                'fee' => $apt->doctor->doctorProfile->fee ?? 0,
                'discount' => $apt->discount ?? 0,
                'final_amount' => $apt->final_amount ?? 0,
            ])
        ]);
    }

    /**
     * Download PATIENT HISTORY PDF (NOT appointment slip)
     */
    public function downloadAppointmentPdf($id)
    {
        $appointment = Appointment::with(['patient', 'doctor.doctorProfile'])
            ->findOrFail($id);

        $this->authorizeDoctor($appointment);

        $previousAppointments = $this->getPatientHistory($appointment, 50);

        return Pdf::loadView(
            'pdf.patient_history',
            compact('appointment', 'previousAppointments')
        )->download(
            'patient-history-' .
            Str::slug($appointment->patient->name) .
            '.pdf'
        );
    }

    /* =========================
       Helper Methods
       ========================= */

    private function authorizeDoctor(Appointment $appointment)
    {
        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }
    }

    private function approveAppointment(Appointment $appointment)
    {
        $fee = $appointment->doctor->doctorProfile->fee ?? 0;
        $appointment->final_amount = $fee - ($appointment->discount ?? 0);

        $todayTokens = QueueTicket::whereHas('appointment', function ($q) use ($appointment) {
            $q->where('doctor_id', $appointment->doctor_id)
              ->whereDate('appointment_date', $appointment->appointment_date);
        })->count();

        QueueTicket::firstOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'token_number' => $todayTokens + 1,
                'status' => 'waiting'
            ]
        );
    }

    private function completeAppointment(Appointment $appointment)
    {
        QueueTicket::where('appointment_id', $appointment->id)
            ->update(['status' => 'done']);

        if (!$appointment->final_amount) {
            $fee = $appointment->doctor->doctorProfile->fee ?? 0;
            $appointment->final_amount = $fee - ($appointment->discount ?? 0);
        }
    }

    /**
     * FIXED: Patient history now shows correctly
     */
private function getPatientHistory(Appointment $appointment, $limit = 50)
{
    return Appointment::with('doctor.doctorProfile')
        ->where('patient_id', $appointment->patient_id)
        ->orderByDesc('appointment_date')
        ->take($limit)
        ->get();
}


    private function getStatusBadge($status)
    {
        return [
            'pending'   => '<span class="badge bg-warning text-dark">Pending</span>',
            'approved'  => '<span class="badge bg-success">Approved</span>',
            'completed' => '<span class="badge bg-secondary">Completed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        ][$status] ?? '<span class="badge bg-dark">' . ucfirst($status) . '</span>';
    }
}
