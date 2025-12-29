<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMS - Hospital Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            padding: 100px 0;
            color: white;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .navbar-custom {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }
        .stats-card {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            color: white;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-heartbeat me-2"></i>HMS Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
    <li class="nav-item">
        @if(auth()->user()->role === 'admin')
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        @elseif(auth()->user()->role === 'doctor')
            <a class="nav-link" href="{{ route('doctor.dashboard') }}">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        @else
            <a class="nav-link" href="{{ route('patient.dashboard') }}">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
            </a>
        @endif
    </li>
@else

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-3 fw-bold mb-4">Hospital Management System</h1>
            <p class="lead mb-5">Modern Healthcare Management Solution</p>
            
            <div class="row justify-content-center mb-5">
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-user-md fa-2x mb-2"></i>
                        <div class="stats-number">50+</div>
                        <div>Expert Doctors</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <div class="stats-number">1000+</div>
                        <div>Happy Patients</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                        <div class="stats-number">5000+</div>
                        <div>Appointments</div>
                    </div>
                </div>
            </div>

            @guest
                <a href="{{ route('register') }}" class="btn btn-custom btn-lg me-3">
                    <i class="fas fa-user-plus me-2"></i>Get Started
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </a>
            @endguest
        </div>
    </div>

    <!-- Features Section -->
    <div class="container pb-5">
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Easy Appointment Booking</h3>
                    <p class="text-muted">Book appointments with your preferred doctors in just a few clicks. No hassle, no waiting.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    <h3>Expert Medical Care</h3>
                    <p class="text-muted">Access to qualified specialists across various medical departments.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-medical-alt"></i>
                    </div>
                    <h3>Digital Records</h3>
                    <p class="text-muted">Your complete medical history at your fingertips. Download reports anytime.</p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Queue Management</h3>
                    <p class="text-muted">Real-time queue status and token numbers for efficient patient flow.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p class="text-muted">Access the system from anywhere on any device.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Private</h3>
                    <p class="text-muted">Your data is encrypted and protected with industry-standard security.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>