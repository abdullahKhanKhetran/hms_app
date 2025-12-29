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
                                    <td>{{ \Carbon\Carbon::parse($apt->appointment_date)->format('d M Y') }}</td>
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
                                            <button class="btn btn-sm btn-success approve-btn" 
                                                    data-id="{{ $apt->id }}" 
                                                    data-fee="{{ $apt->doctor->doctorProfile->fee ?? 0 }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            
                                            <form action="{{ route('doctor.update_status', $apt->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </form>

                                        @elseif($apt->status == 'approved')
                                            <button class="btn btn-sm btn-primary complete-btn" 
                                                    data-id="{{ $apt->id }}"
                                                    data-remarks="{{ $apt->doctor_remarks }}">
                                                <i class="fas fa-clipboard-check"></i> Complete
                                            </button>
                                            
                                            <button class="btn btn-sm btn-info view-history-btn" 
                                                    data-id="{{ $apt->id }}">
                                                <i class="fas fa-history"></i> View History
                                            </button>

                                        @elseif($apt->status == 'completed')
                                            <button class="btn btn-sm btn-info view-history-btn" 
                                                    data-id="{{ $apt->id }}">
                                                <i class="fas fa-eye"></i> View Report
                                            </button>
                                            
                                            <a href="{{ route('doctor.download_pdf', $apt->id) }}" 
                                               class="btn btn-sm btn-outline-dark">
                                                <i class="fas fa-download"></i> PDF
                                            </a>
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

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approve Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status" value="approved">
                    
                    <div class="mb-3">
                        <label class="form-label">Doctor Fee</label>
                        <input type="text" id="feeDisplay" class="form-control" readonly>
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

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="completeForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Complete Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="status" value="completed">
                    
                    <div class="mb-3">
                        <label class="form-label">Final Remarks</label>
                        <textarea name="doctor_remarks" id="completeRemarks" class="form-control" rows="3" placeholder="Add final diagnosis or prescription..."></textarea>
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

<!-- View History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Patient History & Current Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historyContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="downloadPdfBtn">
                    <i class="fas fa-download"></i> Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentAppointmentId = null;

// Handle Approve Button Clicks
document.querySelectorAll('.approve-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const aptId = this.dataset.id;
        const fee = this.dataset.fee;
        
        document.getElementById('approveForm').action = `/doctor/appointment/${aptId}/update`;
        document.getElementById('feeDisplay').value = `Rs. ${fee}`;
        document.querySelector('#approveForm [name="discount"]').value = 0;
        document.querySelector('#approveForm [name="doctor_remarks"]').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('approveModal'));
        modal.show();
    });
});

// Handle Complete Button Clicks
document.querySelectorAll('.complete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const aptId = this.dataset.id;
        const remarks = this.dataset.remarks;
        
        document.getElementById('completeForm').action = `/doctor/appointment/${aptId}/update`;
        document.getElementById('completeRemarks').value = remarks || '';
        
        const modal = new bootstrap.Modal(document.getElementById('completeModal'));
        modal.show();
    });
});

// Handle View History Button Clicks
document.querySelectorAll('.view-history-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const aptId = this.dataset.id;
        currentAppointmentId = aptId;
        
        const modal = new bootstrap.Modal(document.getElementById('historyModal'));
        modal.show();
        
        // Load history via AJAX
        fetch(`/doctor/appointment/${aptId}/preview`)
            .then(response => response.json())
            .then(data => {
                renderHistoryContent(data);
            })
            .catch(error => {
                document.getElementById('historyContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading data</div>';
            });
    });
});

function renderHistoryContent(data) {
    let html = `
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Current Appointment Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Patient:</strong> ${data.current.patient_name}</p>
                        <p><strong>Date:</strong> ${data.current.date}</p>
                        <p><strong>Status:</strong> ${data.current.status_badge}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fee:</strong> Rs. ${data.current.fee}</p>
                        <p><strong>Discount:</strong> Rs. ${data.current.discount}</p>
                        <p><strong>Final Amount:</strong> Rs. ${data.current.final_amount}</p>
                    </div>
                </div>
                ${data.current.remarks ? `
                    <div class="alert alert-info mt-3">
                        <strong>Remarks:</strong><br>${data.current.remarks}
                    </div>
                ` : ''}
            </div>
        </div>
    `;

    if (data.history.length > 0) {
        html += `
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Previous Visit History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Doctor</th>
                                    <th>Department</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        data.history.forEach(record => {
            html += `
                <tr>
                    <td>${record.date}</td>
                    <td>${record.doctor}</td>
                    <td>${record.department}</td>
                    <td>${record.remarks}</td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    } else {
        html += `
            <div class="alert alert-warning">
                No previous visit history found.
            </div>
        `;
    }

    document.getElementById('historyContent').innerHTML = html;
}

// Handle PDF Download from modal
document.getElementById('downloadPdfBtn').addEventListener('click', function() {
    if (currentAppointmentId) {
        window.location.href = `/doctor/appointment/${currentAppointmentId}/download-pdf`;
    }
});
</script>
@endsection