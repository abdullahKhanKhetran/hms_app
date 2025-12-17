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
}