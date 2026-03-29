<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recovery when wallet/OTP tables are missing but migrations may already be recorded.
 */
return new class extends Migration
{
    public function up(): void
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

        if (Schema::hasTable('users')) {
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

    public function down(): void
    {
        //
    }
};
