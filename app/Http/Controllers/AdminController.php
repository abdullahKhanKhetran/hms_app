<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Fetch stats for the dashboard
        $totalDoctors = User::where('role', 1)->count();
        $totalPatients = User::where('role', 0)->count();
        $appointments = Appointment::count();

        return view('admin.dashboard', compact('totalDoctors', 'totalPatients', 'appointments'));
    }

    public function createDoctor()
    {
        return view('admin.create_doctor');
    }

    public function storeDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'specialization' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Use Transaction to ensure both User and Profile are created, or neither
        DB::transaction(function () use ($request) {
            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 1, // Doctor Role
            ]);

            // 2. Create Profile
            DoctorProfile::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'qualification' => $request->qualification,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);
        });

        return redirect()->route('admin.dashboard')->with('success', 'Doctor added successfully!');
    }

    public function allAppointments()
    {
        $appointments = Appointment::with(['doctor', 'patient'])->orderBy('created_at', 'desc')->get();
        return view('admin.appointments', compact('appointments'));
    }
}