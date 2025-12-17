@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">My Health Dashboard</h2>
        <p class="text-muted">Manage your appointments and history</p>
    </div>
</div>

<div class="row g-4">
    {{-- Booking Card --}}
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

    {{-- Recent History --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Recent History</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($appointments as $apt)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold d-block">Dr. {{ $apt->doctor->name }}</span>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d M Y') }}</small>
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
                    @empty
                        <li class="list-group-item text-muted">No appointment history yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Doctor Availability Section (separate) --}}
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Doctor Availability</h5>
            </div>

            <div class="card-body">
                @if($doctors->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Specialization</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Reserved Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($doctors as $doctor)
                                    <tr>
                                        {{-- Doctor Name --}}
                                        <td class="fw-bold">Dr. {{ $doctor->name }}</td>

                                        {{-- Specialization --}}
                                        <td>{{ $doctor->doctorProfile->specialization ?? 'N/A' }}</td>

                                        {{-- Fee --}}
                                        <td>Rs. {{ $doctor->doctorProfile->fee ?? 'N/A' }}</td>

                                        {{-- Status --}}
                                        <td>
                                            @if($doctor->availability === 'free')
                                                <span class="badge bg-success px-3 py-2">Free</span>
                                            @else
                                                <span class="badge bg-danger px-3 py-2">Reserved</span>
                                            @endif
                                        </td>

                                        {{-- Reserved Date --}}
                                        <td>
                                            @if($doctor->availability === 'reserved')
                                                {{ \Carbon\Carbon::parse($doctor->reserved_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">No doctors found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
