<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    public const STATUS_APPLIED = 'applied';

    public const STATUS_SELECTED = 'selected';

    public const STATUS_REJECTED = 'rejected';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function postedJob(): BelongsTo
    {
        return $this->belongsTo(PostedJob::class, 'job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
