<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\QueueController; // <--- ADDED THIS IMPORT

// Public Routes
Route::get('/', function () { return view('welcome'); }); // Landing Page
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Live Queue Screen (Public - placed here to avoid middleware conflicts)
Route::get('/live-queue', [QueueController::class, 'liveScreen'])->name('queue.live');

// Admin Routes (Protected)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/add-doctor', [AdminController::class, 'createDoctor'])->name('admin.create_doctor');
    Route::post('/admin/add-doctor', [AdminController::class, 'storeDoctor'])->name('admin.store_doctor');
    Route::get('/admin/appointments', [AdminController::class, 'allAppointments'])->name('admin.appointments');
});

// Doctor Routes (Protected)
Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/dashboard', [DoctorController::class, 'dashboard'])->name('doctor.dashboard');
    Route::post('/doctor/appointment/{id}/update', [DoctorController::class, 'updateStatus'])->name('doctor.update_status');
});

// Patient Routes (Protected)
Route::middleware(['auth', 'role:patient'])->group(function () {
    // UNCOMMENTED AND FIXED:
    Route::get('/patient/dashboard', [PatientController::class, 'dashboard'])->name('patient.dashboard');
    
    Route::get('/patient/book', [PatientController::class, 'showBookingForm'])->name('patient.book_form');
    Route::post('/patient/book', [PatientController::class, 'bookAppointment'])->name('patient.book_store');
    Route::get('/patient/appointment/{id}/pdf', [PatientController::class, 'downloadSlip'])->name('patient.download_pdf');
});