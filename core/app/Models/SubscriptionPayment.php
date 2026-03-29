<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const PAYER_USER = 'user';

    public const PAYER_BUYER = 'buyer';

    protected $fillable = [
        'payer_type',
        'user_id',
        'buyer_id',
        'plan_id',
        'order_id',
        'payment_id',
        'amount_paise',
        'currency',
        'status',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'buyer_id' => 'integer',
            'plan_id' => 'integer',
            'amount_paise' => 'integer',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
