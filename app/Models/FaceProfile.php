<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'profile_key', 'embedding_hash', 'samples_count', 'is_active', 'last_verified_at'];
    protected function casts(): array { return ['is_active' => 'boolean', 'last_verified_at' => 'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
