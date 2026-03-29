<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostedJob extends Model
{
    public const TYPE_ARTICLESHIP = 'articleship';

    public const TYPE_SHORT_TERM = 'short_term';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_FILLED = 'filled';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'stipend' => 'decimal:2',
            'per_day_pay' => 'decimal:2',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function scopeOpenForListing($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }
}
