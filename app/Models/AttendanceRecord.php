<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'school_class_id', 'attendance_date', 'check_in_time', 'check_out_time', 'status', 'source_event_id'];
    protected function casts(): array { return ['attendance_date' => 'date']; }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
