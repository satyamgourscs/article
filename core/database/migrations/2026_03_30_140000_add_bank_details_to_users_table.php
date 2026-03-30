<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name', 191)->nullable();
            }
            if (! Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number', 64)->nullable();
            }
            if (! Schema::hasColumn('users', 'bank_ifsc')) {
                $table->string('bank_ifsc', 32)->nullable();
            }
            if (! Schema::hasColumn('users', 'bank_account_holder_name')) {
                $table->string('bank_account_holder_name', 191)->nullable();
            }
            if (! Schema::hasColumn('users', 'upi_id')) {
                $table->string('upi_id', 255)->nullable();
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
                'bank_name',
                'bank_account_number',
                'bank_ifsc',
                'bank_account_holder_name',
                'upi_id',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
