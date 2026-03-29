<?php

namespace App\Support;

/**
 * Reference list of schema required by new systems (OTP, wallet, subscriptions, job portal).
 * Use missingNewSystemTables() / missingUserReferralColumns() against the live DB.
 *
 * Note: Auth "role" is not a column on users in this codebase; portfolio entries use role on portfolios.
 */
final class DatabaseSchemaAudit
{
    /** @var list<string> */
    public const NEW_SYSTEM_TABLES = [
        'otp_verifications',
        'wallets',
        'wallet_transactions',
        'wallet_withdraw_requests',
        'plans',
        'user_subscriptions',
        'buyer_subscriptions',
        'posted_jobs',
        'job_applications',
        'student_profiles',
        'company_profiles',
    ];

    /** Referrer stored as FK; no separate string `referred_by` column in code. */
    public const USER_REFERRAL_COLUMNS = [
        'referral_code',
        'referred_by_user_id',
    ];

    /**
     * @return list<string>
     */
    public static function missingNewSystemTables(): array
    {
        $missing = [];
        foreach (self::NEW_SYSTEM_TABLES as $table) {
            if (! SafeSchema::hasTable($table)) {
                $missing[] = $table;
            }
        }

        return $missing;
    }

    /**
     * @return list<string>
     */
    public static function missingUserReferralColumns(): array
    {
        if (! SafeSchema::hasTable('users')) {
            return [...self::USER_REFERRAL_COLUMNS];
        }

        $missing = [];
        foreach (self::USER_REFERRAL_COLUMNS as $col) {
            if (! SafeSchema::hasColumn('users', $col)) {
                $missing[] = $col;
            }
        }

        return $missing;
    }
}
