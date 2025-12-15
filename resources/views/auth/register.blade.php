@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-success">Create Account</h3>
                    <p class="text-muted">Join us as a Patient</p>
                </div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-secondary">Full Name</label>
                        <input type="text" name="name" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg text-white">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection