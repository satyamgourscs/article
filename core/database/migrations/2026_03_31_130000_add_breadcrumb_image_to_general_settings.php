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
            if (! Schema::hasColumn('general_settings', 'breadcrumb_image')) {
                $table->string('breadcrumb_image', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('general_settings')) {
            return;
        }

        Schema::table('general_settings', function (Blueprint $table) {
            if (Schema::hasColumn('general_settings', 'breadcrumb_image')) {
                $table->dropColumn('breadcrumb_image');
            }
        });
    }
};
