<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_profiles')) {
            return;
        }

        Schema::table('company_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('company_profiles', 'pincode')) {
                $table->string('pincode', 32)->nullable();
            }
            if (! Schema::hasColumn('company_profiles', 'country')) {
                $table->string('country', 191)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_profiles')) {
            return;
        }

        Schema::table('company_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('company_profiles', 'country')) {
                $table->dropColumn('country');
            }
            if (Schema::hasColumn('company_profiles', 'pincode')) {
                $table->dropColumn('pincode');
            }
        });
    }
};
