<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'request_type', 'reason', 'description', 'start_date', 'end_date', 'status', 'reviewed_by', 'reviewed_at'];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date', 'reviewed_at' => 'datetime']; }
}
