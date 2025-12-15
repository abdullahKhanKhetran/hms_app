<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\DoctorProfile;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function dashboard()
    {
        $appointments = Appointment::where('patient_id', Auth::id())
                        ->with('doctor') // Load doctor details
                        ->orderBy('appointment_date', 'desc')
                        ->get();

        return view('patient.dashboard', compact('appointments'));
    }

    public function showBookingForm()
    {
        // Get all doctors with their user info
        $doctors = DoctorProfile::with('user')->where('is_available', true)->get();
        return view('patient.book', compact('doctors'));
    }

    public function bookAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
        ]);

        Appointment::create([
            'patient_id' => Auth::id(),
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'status' => 'pending'
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Appointment booked successfully!');
    }

    public function downloadSlip($id)
{
    $appointment = Appointment::with(['doctor.doctorProfile', 'patient'])->findOrFail($id);

    // Security: Only the patient who owns the appointment can download it
    if ($appointment->patient_id != Auth::id()) {
        abort(403);
    }

    $pdf = Pdf::loadView('pdf.slip', compact('appointment'));
    return $pdf->download('appointment-slip-'.$id.'.pdf');
}
}