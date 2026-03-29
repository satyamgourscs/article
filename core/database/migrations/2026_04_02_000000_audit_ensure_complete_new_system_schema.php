<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Full audit / recovery: ensures every table and user column used by OTP, wallet,
 * subscriptions, job portal, and referrals — idempotent (only creates/adds missing pieces).
 *
 * Run on SQL-dump databases where granular migration rows are missing:
 *   php artisan migrate --path=database/migrations/2026_04_02_000000_audit_ensure_complete_new_system_schema.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->ensurePlansAndSubscriptions();
        $this->ensureOtpAndWallets();
        $this->ensureUserReferralColumns();
        $this->ensureStudentProfiles();
        $this->ensureJobPortal();
    }

    private function ensureStudentProfiles(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

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

    private function ensurePlansAndSubscriptions(): void
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

    private function ensureOtpAndWallets(): void
    {
        if (! Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('contact', 191);
                $table->string('otp', 8);
                $table->timestamp('expires_at');
                $table->boolean('is_used')->default(false);
                $table->unsignedTinyInteger('verify_attempts')->default(0);
                $table->string('guard_target', 32)->default('web');
                $table->timestamps();
                $table->index(['contact', 'is_used']);
            });
        }

        if (! Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->decimal('balance', 28, 8)->default(0);
                $table->decimal('total_earned', 28, 8)->default(0);
                $table->decimal('total_withdrawn', 28, 8)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('wallet_transactions')) {
            Schema::create('wallet_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 28, 8);
                $table->string('type', 16);
                $table->string('source', 64)->nullable();
                $table->text('meta')->nullable();
                $table->timestamps();
                $table->index('user_id');
            });
        }

        if (! Schema::hasTable('wallet_withdraw_requests')) {
            Schema::create('wallet_withdraw_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->decimal('amount', 28, 8);
                $table->string('status', 32)->default('pending');
                $table->text('note')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }
    }

    private function ensureUserReferralColumns(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'referral_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('referral_code', 32)->nullable()->unique();
            });
        }

        if (! Schema::hasColumn('users', 'referred_by_user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('referred_by_user_id')->nullable()->index();
            });
        }
    }

    private function ensureJobPortal(): void
    {
        if (! Schema::hasTable('buyers')) {
            return;
        }

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

        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasTable('job_applications')) {
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
    }

    public function down(): void
    {
        //
    }
};
