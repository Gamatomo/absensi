<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['school_class_id', 'teacher_id', 'subject', 'day_of_week', 'start_time', 'end_time', 'room'];
}
