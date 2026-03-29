<?php

namespace App\Console\Commands;

use App\Support\DatabaseSchemaAudit;
use Illuminate\Console\Command;

class AuditDatabaseSchema extends Command
{
    protected $signature = 'db:audit-schema';

    protected $description = 'List missing tables/columns for OTP, wallet, subscriptions, and job portal';

    public function handle(): int
    {
        $missingTables = DatabaseSchemaAudit::missingNewSystemTables();
        $missingCols = DatabaseSchemaAudit::missingUserReferralColumns();

        $this->line('Required new-system tables: '.implode(', ', DatabaseSchemaAudit::NEW_SYSTEM_TABLES));
        $this->line('Required users.referral columns: '.implode(', ', DatabaseSchemaAudit::USER_REFERRAL_COLUMNS));
        $this->newLine();

        if ($missingTables === [] && $missingCols === []) {
            $this->info('OK: nothing missing from this audit set.');

            return self::SUCCESS;
        }

        if ($missingTables !== []) {
            $this->error('Missing tables ('.count($missingTables).'):');
            foreach ($missingTables as $t) {
                $this->line('  - '.$t);
            }
        }

        if ($missingCols !== []) {
            $this->error('Missing users columns ('.count($missingCols).'):');
            foreach ($missingCols as $c) {
                $this->line('  - '.$c);
            }
        }

        $this->newLine();
        $this->warn('Fix (SQL-dump DBs): php artisan migrate --path=database/migrations/2026_04_02_000000_audit_ensure_complete_new_system_schema.php --force');

        return self::SUCCESS;
    }
}
