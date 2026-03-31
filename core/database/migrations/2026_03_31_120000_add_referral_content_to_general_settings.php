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
            if (! Schema::hasColumn('general_settings', 'referral_image')) {
                $table->string('referral_image', 255)->nullable();
            }
            if (! Schema::hasColumn('general_settings', 'referral_description')) {
                $table->text('referral_description')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            foreach (['referral_image', 'referral_description'] as $col) {
                if (Schema::hasColumn('general_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
