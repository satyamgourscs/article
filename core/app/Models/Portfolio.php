<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'role',
        'url',
        'skill_ids',
        'status',
    ];

    protected $casts = [
        'skill_ids' => 'array',
    ];
}
