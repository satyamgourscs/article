<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    public const TYPE_STUDENT = 'student';

    public const TYPE_COMPANY = 'company';

    protected $fillable = [
        'name',
        'type',
        'price',
        'duration_days',
        'job_apply_limit',
        'job_view_limit',
        'job_post_limit',
        'listing_visible_jobs',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'duration_days' => 'integer',
            'job_apply_limit' => 'integer',
            'job_view_limit' => 'integer',
            'job_post_limit' => 'integer',
            'listing_visible_jobs' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function buyerSubscriptions(): HasMany
    {
        return $this->hasMany(BuyerSubscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStudent($query)
    {
        return $query->where('type', self::TYPE_STUDENT);
    }

    public function scopeCompany($query)
    {
        return $query->where('type', self::TYPE_COMPANY);
    }
}
