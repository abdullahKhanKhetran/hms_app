<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'token_number',
        'status'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}