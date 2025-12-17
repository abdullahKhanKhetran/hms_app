@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Doctor Portal</h2>
        <p class="text-muted">Welcome, Dr. {{ Auth::user()->name }}</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
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
                                <th>Date</th>
                                <th>Fee</th>
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
                                    <td>Rs. {{ $apt->doctor->doctorProfile->fee ?? 0 }}</td>
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
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $apt->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            
                                            <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </form>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal{{ $apt->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Approve Appointment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="status" value="approved">
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label">Doctor Fee</label>
                                                                    <input type="text" class="form-control" value="Rs. {{ $apt->doctor->doctorProfile->fee ?? 0 }}" readonly>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Discount (Rs)</label>
                                                                    <input type="number" name="discount" class="form-control" value="0" min="0" step="0.01">
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Remarks/Notes</label>
                                                                    <textarea name="doctor_remarks" class="form-control" rows="3" placeholder="Add prescription or diagnosis notes..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-success">Approve & Save</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        @elseif($apt->status == 'approved')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#completeModal{{ $apt->id }}">
                                                <i class="fas fa-clipboard-check"></i> Complete
                                            </button>

                                            <!-- Complete Modal -->
                                            <div class="modal fade" id="completeModal{{ $apt->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Complete Appointment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="status" value="completed">
                                                                
                                                                <div class="mb-3">
                                                                    <label class="form-label">Final Remarks</label>
                                                                    <textarea name="doctor_remarks" class="form-control" rows="3" placeholder="Add final diagnosis or prescription...">{{ $apt->doctor_remarks }}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Mark as Completed</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No upcoming appointments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection