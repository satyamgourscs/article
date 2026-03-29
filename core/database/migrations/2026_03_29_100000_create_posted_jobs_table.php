<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Real job postings (articleship / short-term audit).
 * Separate from legacy marketplace `jobs` (bidding) table.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posted_jobs')) {
            return;
        }

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

    public function down(): void
    {
        Schema::dropIfExists('posted_jobs');
    }
};
