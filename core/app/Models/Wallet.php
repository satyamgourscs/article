<?php

namespace App\Models;

use App\Support\SafeSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_withdrawn',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'float',
            'total_earned' => 'float',
            'total_withdrawn' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Align wallets.balance with users.balance when the ledger was updated outside the wallet service.
     */
    public static function syncBalanceMirrorFromUser(User $user): void
    {
        if (! SafeSchema::hasTable('wallets') || ! $user->id) {
            return;
        }

        $wallet = static::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
        );
        $wallet->balance = (float) $user->balance;
        $wallet->save();
    }
}
