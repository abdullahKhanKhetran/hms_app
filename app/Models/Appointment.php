<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'status',
        'notes',
        'discount',
        'final_amount',
        'doctor_remarks'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
    
    public function queueTicket()
    {
        return $this->hasOne(QueueTicket::class);
    }
    
    // Helper to calculate final amount
    public function calculateFinalAmount()
    {
        $fee = $this->doctor->doctorProfile->fee ?? 0;
        $this->final_amount = $fee - $this->discount;
        $this->save();
    }
}