@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Welcome Back</h3>
                    <p class="text-muted">Please login to your account</p>
                </div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-secondary">Email Address</label>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="name@example.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="********" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Login</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <small>Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none">Register here</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection