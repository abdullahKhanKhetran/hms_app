<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QueueTicket;
use Carbon\Carbon;

class QueueController extends Controller
{
    public function liveScreen()
    {
        // Get tickets for Today only
        $today = Carbon::today();

        $tickets = QueueTicket::whereHas('appointment', function($q) use ($today) {
            $q->whereDate('appointment_date', $today);
        })->with(['appointment.doctor.doctorProfile', 'appointment.patient'])
          ->whereIn('status', ['waiting', 'serving'])
          ->orderBy('token_number', 'asc')
          ->get();

        return view('queue.live', compact('tickets'));
    }
}