<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Queue Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #222; color: white; font-family: 'Segoe UI', sans-serif; }
        .token-card { background: #333; border: 2px solid #555; border-radius: 10px; margin-bottom: 20px; }
        .token-number { font-size: 4rem; font-weight: bold; color: #00ba94; }
        .doctor-name { font-size: 1.5rem; color: #ddd; }
        .status-waiting { color: #ffc107; font-weight: bold; }
        .header { background: #0061f2; padding: 20px; text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>🏥 HMS Live Queue Status</h1>
        <p class="mb-0">{{ date('l, F j, Y') }}</p>
    </div>

    <div class="container">
        <div class="row">
            @forelse($tickets as $ticket)
                <div class="col-md-4">
                    <div class="token-card p-4 text-center">
                        <h5 class="text-uppercase text-muted mb-3">Token Number</h5>
                        <div class="token-number">{{ str_pad($ticket->token_number, 3, '0', STR_PAD_LEFT) }}</div>
                        <hr class="border-secondary">
                        <div class="doctor-name">Dr. {{ $ticket->appointment->doctor->name }}</div>
                        <div class="mt-2">
                            Patient: {{ $ticket->appointment->patient->name }}
                        </div>
                        <div class="mt-3 status-waiting">
                            <span class="spinner-grow spinner-grow-sm" role="status"></span> Waiting
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center mt-5">
                    <h3 class="text-muted">No active queue at the moment.</h3>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        setTimeout(function(){
           window.location.reload(1);
        }, 10000);
    </script>
</body>
</html>