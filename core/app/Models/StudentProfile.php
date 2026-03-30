<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'qualification',
        'education_status',
        'preferred_domains',
        'preferred_state',
        'preferred_city',
        'skills',
        'training_experience',
        'resume_path',
        'expertise_level',
        'experience_years',
    ];

    protected function casts(): array
    {
        return [
            'preferred_domains' => 'array',
            'skills' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function domainLabels(): array
    {
        $map = config('student_profile.domains', []);

        return array_values(array_intersect_key(
            $map,
            array_flip($this->preferred_domains ?? [])
        ));
    }
}
