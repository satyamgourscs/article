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

        if (! Schema::hasColumn('company_profiles', 'state')) {
            Schema::table('company_profiles', function (Blueprint $table) {
                $table->string('state', 191)->nullable()->after('firm_type');
            });
        }

        if (! Schema::hasColumn('company_profiles', 'city')) {
            Schema::table('company_profiles', function (Blueprint $table) {
                $table->string('city', 191)->nullable()->after('state');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_profiles')) {
            return;
        }

        Schema::table('company_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('company_profiles', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('company_profiles', 'state')) {
                $table->dropColumn('state');
            }
        });
    }
};
