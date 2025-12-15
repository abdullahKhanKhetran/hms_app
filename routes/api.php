<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Public API Routes
Route::post('/register', [AuthController::class, 'apiRegister']);
Route::post('/login', [AuthController::class, 'apiLogin']);

// Protected API Routes (Requires Token)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'apiLogout']);
    
    // Example: API for fetching doctors (Public or Protected depending on need)
    Route::get('/doctors', [AdminController::class, 'getAllDoctorsApi']);
});