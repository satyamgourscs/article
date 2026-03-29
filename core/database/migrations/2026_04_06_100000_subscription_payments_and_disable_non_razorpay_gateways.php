<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_payments')) {
            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payer_type', 16);
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('buyer_id')->nullable()->index();
                $table->unsignedBigInteger('plan_id')->index();
                $table->string('order_id')->index();
                $table->string('payment_id')->nullable()->index();
                $table->unsignedBigInteger('amount_paise');
                $table->string('currency', 8)->default('INR');
                $table->string('status', 24)->default('pending');
                $table->string('failure_reason')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('gateways')) {
            DB::table('gateways')->update(['status' => 0]);
            DB::table('gateways')->where('alias', 'Razorpay')->update(['status' => 1]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
