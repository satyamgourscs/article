<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds profile convenience / mirror columns. Does not remove or rename existing columns.
 * Legacy columns remain canonical: country_name, bank_account_number, bank_ifsc, zip, referred_by_user_id, referral_code.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pincode')) {
                $table->string('pincode', 32)->nullable();
            }
            if (! Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable();
            }
            if (! Schema::hasColumn('users', 'preferred_state')) {
                $table->string('preferred_state', 191)->nullable();
            }
            if (! Schema::hasColumn('users', 'preferred_city')) {
                $table->string('preferred_city', 191)->nullable();
            }
            if (! Schema::hasColumn('users', 'referred_by')) {
                $table->string('referred_by', 64)->nullable();
            }
            if (! Schema::hasColumn('users', 'account_number')) {
                $table->string('account_number', 64)->nullable();
            }
            if (! Schema::hasColumn('users', 'ifsc_code')) {
                $table->string('ifsc_code', 32)->nullable();
            }
            if (! Schema::hasColumn('users', 'account_holder_name')) {
                $table->string('account_holder_name', 191)->nullable();
            }
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country', 191)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'pincode',
                'skills',
                'preferred_state',
                'preferred_city',
                'referred_by',
                'account_number',
                'ifsc_code',
                'account_holder_name',
                'country',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
