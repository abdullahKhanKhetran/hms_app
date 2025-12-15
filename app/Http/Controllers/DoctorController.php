<?php

namespace App\Http\Controllers;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        // Get appointments for the logged-in doctor
        $appointments = Appointment::where('doctor_id', Auth::id())
                        ->with('patient') // Load patient details
                        ->where('appointment_date', '>=', now()->toDateString()) // Only today and future
                        ->orderBy('appointment_date', 'asc')
                        ->get();

        return view('doctor.dashboard', compact('appointments'));
    }
    
    // Add logic to approve later if needed
    // Add to App\Http\Controllers\DoctorController.php
public function updateStatus(Request $request, $id)
{
    $appointment = Appointment::findOrFail($id);
    
    if ($appointment->doctor_id != Auth::id()) {
        abort(403);
    }

    $request->validate([
        'status' => 'required|in:approved,cancelled,completed'
    ]);

    // Logic for generating a Token when Approved
    if ($request->status == 'approved' && $appointment->status != 'approved') {
        
        // Count how many tokens exist for this doctor today to find the next number
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

    // Logic for marking Ticket as "Done" when appointment is Completed
    if ($request->status == 'completed') {
        $ticket = QueueTicket::where('appointment_id', $appointment->id)->first();
        if ($ticket) {
            $ticket->update(['status' => 'done']);
        }
    }

    $appointment->status = $request->status;
    $appointment->save();

    return back()->with('success', 'Status updated and Token generated!');
}
}