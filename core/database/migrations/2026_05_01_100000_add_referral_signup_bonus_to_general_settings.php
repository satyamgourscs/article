<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('general_settings', 'referral_signup_bonus')) {
                $table->decimal('referral_signup_bonus', 28, 8)->default(50);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'referral_signup_bonus')) {
                $table->dropColumn('referral_signup_bonus');
            }
        });
    }
};
