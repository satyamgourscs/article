<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * Memoized table checks so code can degrade gracefully when migrations were not applied.
 */
final class SafeSchema
{
    /** @var array<string, bool> */
    private static array $cache = [];

    /** @var array<string, bool> */
    private static array $columnCache = [];

    public static function hasTable(string $table): bool
    {
        if (array_key_exists($table, self::$cache)) {
            return self::$cache[$table];
        }

        try {
            return self::$cache[$table] = Schema::hasTable($table);
        } catch (\Throwable) {
            return self::$cache[$table] = false;
        }
    }

    public static function hasColumn(string $table, string $column): bool
    {
        $key = $table.'.'.$column;
        if (array_key_exists($key, self::$columnCache)) {
            return self::$columnCache[$key];
        }

        try {
            return self::$columnCache[$key] = Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return self::$columnCache[$key] = false;
        }
    }

    public static function resetCache(): void
    {
        self::$cache = [];
        self::$columnCache = [];
    }

    /** Referral fields used by OTP signup (code uses referred_by_user_id, not a string referred_by column). */
    public static function usersReferralReady(): bool
    {
        return self::hasTable('users')
            && self::hasColumn('users', 'referral_code')
            && self::hasColumn('users', 'referred_by_user_id');
    }

    public static function jobPortalAvailable(): bool
    {
        return self::hasTable('posted_jobs') && self::hasTable('job_applications');
    }

    public static function walletCoreAvailable(): bool
    {
        return self::hasTable('wallets') && self::hasTable('wallet_transactions');
    }

    public static function walletWithdrawalsAvailable(): bool
    {
        return self::hasTable('wallet_withdraw_requests');
    }

    public static function otpVerificationsAvailable(): bool
    {
        return self::hasTable('otp_verifications');
    }

    public static function subscriptionsAvailable(): bool
    {
        return self::hasTable('plans')
            && self::hasTable('user_subscriptions')
            && self::hasTable('buyer_subscriptions');
    }

    public static function subscriptionPaymentsAvailable(): bool
    {
        return self::hasTable('subscription_payments');
    }

    public static function studentWalletFeatureAvailable(): bool
    {
        return self::walletCoreAvailable() && self::walletWithdrawalsAvailable();
    }
}
