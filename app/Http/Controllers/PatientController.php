<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Patient Dashboard
     */
    public function dashboard()
    {
        $today = now()->format('Y-m-d');

        // 1️⃣ Logged-in patient appointments
        $appointments = Appointment::where('patient_id', Auth::id())
            ->with('doctor')
            ->orderBy('appointment_date', 'desc')
            ->get();

        // 2️⃣ Fetch doctors with their future appointments
        $doctors = User::where('role', 1) // Only doctors
            ->whereHas('doctorProfile')
            ->with([
                'doctorProfile',
                'doctorAppointments' => function ($query) use ($today) {
                    $query->whereIn('status', ['pending', 'approved'])
                          ->where('appointment_date', '>=', $today)
                          ->orderBy('appointment_date', 'asc');
                }
            ])
            ->get();

        // 3️⃣ Determine availability
        $doctors->transform(function ($doctor) {
            if ($doctor->doctorAppointments->isNotEmpty()) {
                $doctor->availability = 'reserved';
                $doctor->reserved_date = $doctor->doctorAppointments->first()->appointment_date;
            } else {
                $doctor->availability = 'free';
                $doctor->reserved_date = null;
            }
            return $doctor;
        });

        return view('patient.dashboard', compact('appointments', 'doctors'));
    }

    /**
     * Show booking form
     */
    public function showBookingForm()
    {
        $doctors = User::where('role', 1)
            ->whereHas('doctorProfile')
            ->with('doctorProfile')
            ->get();

        return view('patient.book', compact('doctors'));
    }

    /**
     * Book appointment
     */
    public function bookAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
                Rule::unique('appointments')->where(function ($query) use ($request) {
                    return $query->where('doctor_id', $request->doctor_id)
                                 ->where('appointment_date', $request->appointment_date);
                }),
            ],
        ]);

        // Create appointment as pending
        Appointment::create([
            'patient_id' => Auth::id(),
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('patient.dashboard')
            ->with('success', 'Appointment booked successfully!');
    }

    /**
     * Download appointment slip (PDF)
     */
   public function downloadSlip($id)
{
    $appointment = Appointment::with(['doctor.doctorProfile', 'patient', 'queueTicket'])
        ->findOrFail($id);

    if ($appointment->patient_id !== Auth::id()) {
        abort(403);
    }

    // Get patient's past completed appointments (last 5)
    $pastAppointments = Appointment::where('patient_id', Auth::id())
        ->where('status', 'completed')
        ->where('id', '!=', $id)
        ->with(['doctor.doctorProfile'])
        ->orderBy('appointment_date', 'desc')
        ->take(5)
        ->get();

    $pdf = Pdf::loadView('pdf.slip', compact('appointment', 'pastAppointments'));

    return $pdf->download('appointment-slip-' . $id . '.pdf');
}
}