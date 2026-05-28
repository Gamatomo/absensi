<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'teacher_number', 'subject', 'enrolled_date'];
    protected function casts(): array { return ['enrolled_date' => 'date']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
