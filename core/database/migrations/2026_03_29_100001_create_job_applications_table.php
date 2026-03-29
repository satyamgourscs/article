<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('posted_jobs')) {
            Schema::create('posted_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('type', 32);
                $table->string('domain')->nullable();
                $table->decimal('stipend', 14, 2)->nullable();
                $table->decimal('per_day_pay', 14, 2)->nullable();
                $table->string('duration')->nullable();
                $table->string('location')->nullable();
                $table->string('company_name')->nullable();
                $table->unsignedInteger('open_positions')->nullable();
                $table->unsignedInteger('current_articles')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('buyer_id')->constrained('buyers')->cascadeOnDelete();
                $table->string('status', 16)->default('open');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('job_applications')) {
            return;
        }

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('posted_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('applied_at')->useCurrent();
            $table->string('status', 16)->default('applied');
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
