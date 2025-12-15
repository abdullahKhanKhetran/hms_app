@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Admin Dashboard</h2>
        <p class="text-muted">Manage doctors, patients, and system settings.</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Doctors</h6>
                        <h2 class="display-6 fw-bold">{{ $totalDoctors ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-user-md fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Patients</h6>
                        <h2 class="display-6 fw-bold">{{ $totalPatients ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-procedures fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-2 opacity-75">Total Appointments</h6>
                        <h2 class="display-6 fw-bold">{{ $appointments ?? 0 }}</h2>
                    </div>
                    <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.create_doctor') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-plus me-1"></i> Add Doctor
                </a>

                <a href="{{ route('admin.appointments') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-list me-1"></i> View Appointments
                </a>
                
                <button class="btn btn-outline-info" disabled><i class="fas fa-cog me-1"></i> Settings (Coming Soon)</button>
            </div>
        </div>
    </div>
</div>
@endsection