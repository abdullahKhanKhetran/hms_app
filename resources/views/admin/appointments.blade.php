@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">All Appointments</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Doctor Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $apt)
                    <tr>
                        <td>#{{ $apt->id }}</td>
                        <td>{{ $apt->patient->name }}</td>
                        <td>Dr. {{ $apt->doctor->name }}</td>
                        <td>{{ $apt->appointment_date }}</td>
                        <td>
                            @if($apt->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($apt->status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($apt->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($apt->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection