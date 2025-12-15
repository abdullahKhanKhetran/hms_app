@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Book an Appointment</h4>
            </div>
            <div class="card-body">
                {{-- Added ID 'bookingForm' to target it with JS --}}
                <form action="{{ route('patient.book_store') }}" method="POST" id="bookingForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Doctor</label>
                        <select name="doctor_id" class="form-select form-select-lg" required>
                            <option value="">Choose a specialist...</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->user_id }}">
                                    Dr. {{ $doc->user->name }} ({{ $doc->specialization }})
                                    [{{ $doc->start_time }} - {{ $doc->end_time }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Preferred Date</label>
                        <input type="date" name="appointment_date" class="form-control form-control-lg" required>
                    </div>

                    <div class="d-grid">
                        {{-- Added ID 'submitBtn' --}}
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SIMPLE FIX: This script runs when the form is submitted --}}
<script>
    document.getElementById('bookingForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        // 1. Disable the button so they can't click again
        btn.disabled = true;
        // 2. Change text to give feedback
        btn.innerText = 'Processing...';
    });
</script>
@endsection