<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    public const MAX_VERIFY_ATTEMPTS = 3;

    protected $fillable = [
        'contact',
        'otp',
        'expires_at',
        'is_used',
        'verify_attempts',
        'guard_target',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
            'verify_attempts' => 'integer',
        ];
    }
}
