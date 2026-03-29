<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('student_profiles')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('student_profiles', 'expertise_level')) {
                    $table->string('expertise_level', 64)->nullable()->after('resume_path');
                }
                if (! Schema::hasColumn('student_profiles', 'experience_years')) {
                    $table->string('experience_years', 32)->nullable()->after('expertise_level');
                }
            });
        }

        if (Schema::hasTable('portfolios')) {
            Schema::table('portfolios', function (Blueprint $table) {
                if (! Schema::hasColumn('portfolios', 'url')) {
                    $table->string('url', 512)->nullable()->after('description');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_profiles')) {
            Schema::table('student_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('student_profiles', 'experience_years')) {
                    $table->dropColumn('experience_years');
                }
                if (Schema::hasColumn('student_profiles', 'expertise_level')) {
                    $table->dropColumn('expertise_level');
                }
            });
        }

        if (Schema::hasTable('portfolios') && Schema::hasColumn('portfolios', 'url')) {
            Schema::table('portfolios', function (Blueprint $table) {
                $table->dropColumn('url');
            });
        }
    }
};
