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

        if (! Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code', 32)->nullable()->unique();
            });
        }

        if (! Schema::hasColumn('users', 'referred_by_user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('referred_by_user_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referred_by_user_id')) {
                $table->dropColumn('referred_by_user_id');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'referral_code')) {
                $table->dropColumn('referral_code');
            }
        });
    }
};
