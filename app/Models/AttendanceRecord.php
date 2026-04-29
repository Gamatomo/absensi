<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'timestamp',
        'method',
        'status',
        'location',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
