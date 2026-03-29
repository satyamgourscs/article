<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    public const TYPE_CREDIT = 'credit';

    public const TYPE_DEBIT = 'debit';

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'source',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
