<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('company_profiles')) {
            return;
        }

        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('buyers')->cascadeOnDelete()->unique();
            $table->string('firm_name');
            $table->string('firm_type', 32);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
