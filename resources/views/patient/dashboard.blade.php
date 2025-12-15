@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">My Health Dashboard</h2>
        <p class="text-muted">Manage your appointments and history</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100 border-primary border-2">
            <div class="card-body text-center p-5">
                <i class="fas fa-calendar-plus fa-4x text-primary mb-3"></i>
                <h4 class="fw-bold">Book Appointment</h4>
                <p class="text-muted">Find a doctor and schedule a visit.</p>
               <a href="{{ route('patient.book_form') }}" class="btn btn-primary btn-lg mt-2">Book Now</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Recent History</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
    @foreach($appointments as $apt)
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-bold d-block">Dr. {{ $apt->doctor->name }}</span>
                <small class="text-muted">{{ $apt->appointment_date }}</small>
            </div>
            @if($apt->status == 'pending')
                <span class="badge bg-warning text-dark">Pending</span>
            @elseif($apt->status == 'approved')
                <span class="badge bg-success">Approved</span>
            @else
                <span class="badge bg-secondary">{{ ucfirst($apt->status) }}</span>
            @endif
        </li>
        <li class="list-group-item d-flex justify-content-between align-items-center">
    <div>
        <span class="fw-bold d-block">Dr. {{ $apt->doctor->name }}</span>
        <small class="text-muted">{{ $apt->appointment_date }}</small>
    </div>
    
    <div class="text-end">
        @if($apt->status == 'pending')
            <span class="badge bg-warning text-dark mb-1">Pending</span>
        @elseif($apt->status == 'approved')
            <span class="badge bg-success mb-1">Approved</span>
            <br>
            <a href="{{ route('patient.download_pdf', $apt->id) }}" class="btn btn-sm btn-outline-dark py-0" style="font-size: 0.8rem;">
                <i class="fas fa-download"></i> Slip
            </a>
        @else
            <span class="badge bg-secondary mb-1">{{ ucfirst($apt->status) }}</span>
        @endif
    </div>
</li>
    @endforeach
</ul>
            </div>
        </div>
    </div>
</div>
@endsection