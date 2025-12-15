<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'qualification',
        'start_time',
        'end_time',
        'is_available'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper to get doctor name via relationship
    public function getDoctorNameAttribute()
    {
        return $this->user->name;
    }
}