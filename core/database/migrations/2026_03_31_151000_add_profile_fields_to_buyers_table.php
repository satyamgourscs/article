<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Mirror student (users) profile storage for CA firms where applicable. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('buyers')) {
            return;
        }

        Schema::table('buyers', function (Blueprint $table) {
            if (! Schema::hasColumn('buyers', 'pincode')) {
                $table->string('pincode', 32)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'skills')) {
                $table->json('skills')->nullable();
            }
            if (! Schema::hasColumn('buyers', 'preferred_state')) {
                $table->string('preferred_state', 191)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'preferred_city')) {
                $table->string('preferred_city', 191)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'bank_name')) {
                $table->string('bank_name', 191)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'bank_account_number')) {
                $table->string('bank_account_number', 64)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'bank_ifsc')) {
                $table->string('bank_ifsc', 32)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'bank_account_holder_name')) {
                $table->string('bank_account_holder_name', 191)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'upi_id')) {
                $table->string('upi_id', 255)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'account_number')) {
                $table->string('account_number', 64)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'ifsc_code')) {
                $table->string('ifsc_code', 32)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'account_holder_name')) {
                $table->string('account_holder_name', 191)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'country')) {
                $table->string('country', 191)->nullable();
            }
            if (! Schema::hasColumn('buyers', 'referral_code')) {
                $table->string('referral_code', 32)->nullable()->unique();
            }
            if (! Schema::hasColumn('buyers', 'referred_by_user_id')) {
                $table->unsignedBigInteger('referred_by_user_id')->nullable()->index();
            }
            if (! Schema::hasColumn('buyers', 'referred_by')) {
                $table->string('referred_by', 64)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('buyers')) {
            return;
        }

        Schema::table('buyers', function (Blueprint $table) {
            foreach ([
                'pincode',
                'skills',
                'preferred_state',
                'preferred_city',
                'bank_name',
                'bank_account_number',
                'bank_ifsc',
                'bank_account_holder_name',
                'upi_id',
                'account_number',
                'ifsc_code',
                'account_holder_name',
                'country',
                'referral_code',
                'referred_by_user_id',
                'referred_by',
            ] as $col) {
                if (Schema::hasColumn('buyers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
