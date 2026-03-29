<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('student_profiles')) {
            return;
        }

        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
            $table->string('qualification', 32);
            $table->string('education_status', 32);
            $table->json('preferred_domains');
            $table->string('preferred_state', 191)->nullable();
            $table->string('preferred_city', 191)->nullable();
            $table->text('training_experience')->nullable();
            $table->string('resume_path', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
