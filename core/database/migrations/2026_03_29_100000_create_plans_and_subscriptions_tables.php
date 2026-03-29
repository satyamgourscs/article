<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type', 32);
                $table->decimal('price', 28, 8)->default(0);
                $table->unsignedInteger('duration_days')->default(365);
                $table->unsignedInteger('job_apply_limit')->default(0);
                $table->unsignedInteger('job_view_limit')->default(0);
                $table->unsignedInteger('job_post_limit')->default(0);
                $table->unsignedInteger('listing_visible_jobs')->default(2);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_subscriptions')) {
            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->unsignedInteger('jobs_applied_count')->default(0);
                $table->unsignedInteger('jobs_viewed_count')->default(0);
                $table->unsignedInteger('jobs_posted_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['user_id', 'is_active']);
            });
        }

        if (! Schema::hasTable('buyer_subscriptions')) {
            Schema::create('buyer_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('buyer_id');
                $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
                $table->date('start_date');
                $table->date('end_date');
                $table->unsignedInteger('jobs_applied_count')->default(0);
                $table->unsignedInteger('jobs_viewed_count')->default(0);
                $table->unsignedInteger('jobs_posted_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['buyer_id', 'is_active']);
            });
        }

        if (Schema::hasTable('plans') && DB::table('plans')->count() === 0) {
            DB::table('plans')->insert([
                [
                    'name' => 'Free',
                    'type' => 'student',
                    'price' => 0,
                    'duration_days' => 365,
                    'job_apply_limit' => 5,
                    'job_view_limit' => 10,
                    'job_post_limit' => 0,
                    'listing_visible_jobs' => 2,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Pro',
                    'type' => 'student',
                    'price' => 29.99,
                    'duration_days' => 30,
                    'job_apply_limit' => 999999,
                    'job_view_limit' => 999999,
                    'job_post_limit' => 0,
                    'listing_visible_jobs' => 999999,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Free',
                    'type' => 'company',
                    'price' => 0,
                    'duration_days' => 365,
                    'job_apply_limit' => 0,
                    'job_view_limit' => 0,
                    'job_post_limit' => 2,
                    'listing_visible_jobs' => 999999,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Pro',
                    'type' => 'company',
                    'price' => 99.99,
                    'duration_days' => 30,
                    'job_apply_limit' => 0,
                    'job_view_limit' => 0,
                    'job_post_limit' => 999999,
                    'listing_visible_jobs' => 999999,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('buyer_subscriptions');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('plans');
    }
};
