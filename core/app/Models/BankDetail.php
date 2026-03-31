<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetail extends Model
{
    protected $fillable = [
        'buyer_id',
        'account_holder_name',
        'account_number',
        'bank_name',
        'ifsc',
        'upi_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
