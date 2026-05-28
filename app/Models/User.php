<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'address', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function student(): HasOne { return $this->hasOne(Student::class); }
    public function teacher(): HasOne { return $this->hasOne(Teacher::class); }
    public function parentGuardian(): HasOne { return $this->hasOne(ParentGuardian::class); }
    public function attendanceEvents(): HasMany { return $this->hasMany(AttendanceEvent::class); }
    public function attendanceRecords(): HasMany { return $this->hasMany(AttendanceRecord::class); }
    public function rfidCards(): HasMany { return $this->hasMany(RfidCard::class); }
    public function faceProfiles(): HasMany { return $this->hasMany(FaceProfile::class); }
}
