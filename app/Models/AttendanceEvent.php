<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceEvent extends Model
{
    use HasFactory;

    protected $fillable = ['device_id', 'user_id', 'rfid_uid', 'face_result', 'face_confidence', 'captured_at', 'idempotency_key', 'payload'];
    protected function casts(): array { return ['captured_at' => 'datetime', 'payload' => 'array', 'face_confidence' => 'decimal:2']; }
}
