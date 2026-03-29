<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerSubscription extends Model
{
    protected $fillable = [
        'buyer_id',
        'plan_id',
        'start_date',
        'end_date',
        'jobs_applied_count',
        'jobs_viewed_count',
        'jobs_posted_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'jobs_applied_count' => 'integer',
            'jobs_viewed_count' => 'integer',
            'jobs_posted_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
