@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Doctor Portal</h2>
        <p class="text-muted">Welcome, Dr. {{ Auth::user()->name }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h5 class="mb-0 fw-bold text-primary">Today's Appointments</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Patient Name</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $apt)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ $apt->patient->name }}</div>
                                        <small class="text-muted">{{ $apt->patient->email }}</small>
                                    </td>
                                    <td>{{ $apt->appointment_date }}</td>
                                    <td>
                                        @if($apt->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($apt->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($apt->status == 'completed')
                                            <span class="badge bg-secondary">Completed</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($apt->status == 'pending')
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="btn btn-sm btn-success me-1">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i> Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($apt->status == 'approved')
                                            <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-clipboard-check"></i> Done
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No upcoming appointments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <h5 class="card-title">My Availability</h5>
                <p class="card-text">Status: <span class="badge bg-success">Active</span></p>
                <p class="card-text">Shift: 09:00 AM - 05:00 PM</p>
            </div>
        </div>
    </div>
</div>
@endsection