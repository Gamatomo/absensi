<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'student_number', 'nisn', 'department', 'enrolled_date'];
    protected function casts(): array { return ['enrolled_date' => 'date']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function classes(): BelongsToMany { return $this->belongsToMany(SchoolClass::class, 'class_student')->withTimestamps(); }
}
