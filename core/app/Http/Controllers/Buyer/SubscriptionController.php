<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use App\Services\RazorpaySubscriptionService;
use App\Services\SubscriptionService;
use App\Support\SafeSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class SubscriptionController extends Controller
{
    public function plans()
    {
        if (! SafeSchema::hasTable('plans')) {
            $notify[] = ['warning', __('Plans are not set up yet. Run database migrations (php artisan migrate).')];

            return to_route('buyer.home')->withNotify($notify);
        }

        $pageTitle = 'Upgrade Plan';
        $plans = Plan::company()->active()->orderBy('price')->get();
        $current = subscriptionService()->getActiveBuyerSubscription(auth()->guard('buyer')->id());

        return view('Template::buyer.subscription.plans', compact('pageTitle', 'plans', 'current'));
    }

    public function startPayment(Request $request, $planId)
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            $notify[] = ['warning', __('Subscriptions are not set up yet. Run database migrations (php artisan migrate).')];

            return back()->withNotify($notify);
        }

        $plan = Plan::company()->active()->where('id', $planId)->firstOrFail();
        $buyerId = (int) auth()->guard('buyer')->id();

        if ((float) $plan->price <= 0) {
            try {
                app(SubscriptionService::class)->switchBuyerPlan($buyerId, (int) $plan->id);
            } catch (\Throwable $e) {
                $notify[] = ['error', $e->getMessage()];

                return back()->withNotify($notify);
            }
            $notify[] = ['success', __('Plan updated.')];

            return to_route('buyer.home')->withNotify($notify);
        }

        if (! SafeSchema::subscriptionPaymentsAvailable()) {
            $notify[] = ['error', __('Run database migrations to enable subscription payments (subscription_payments table).')];

            return back()->withNotify($notify);
        }

        $razorpay = app(RazorpaySubscriptionService::class);

        try {
            [$key, $secret] = $razorpay->credentials();
        } catch (\Throwable $e) {
            $notify[] = ['error', $e->getMessage()];

            return back()->withNotify($notify);
        }

        $currency = $razorpay->currency();
        $amountPaise = $razorpay->orderAmountPaise($plan);

        try {
            $api = new Api($key, $secret);
            $order = $api->order->create([
                'receipt' => 'sub_b'.$buyerId.'_p'.$plan->id.'_'.uniqid('', true),
                'amount' => $amountPaise,
                'currency' => $currency,
                'payment_capture' => 1,
            ]);
        } catch (\Throwable $e) {
            $notify[] = ['error', $e->getMessage()];

            return back()->withNotify($notify);
        }

        SubscriptionPayment::query()->create([
            'payer_type' => SubscriptionPayment::PAYER_BUYER,
            'user_id' => null,
            'buyer_id' => $buyerId,
            'plan_id' => $plan->id,
            'order_id' => $order['id'],
            'amount_paise' => $amountPaise,
            'currency' => $currency,
            'status' => SubscriptionPayment::STATUS_PENDING,
        ]);

        $pageTitle = __('Complete payment');
        $buyer = auth()->guard('buyer')->user();
        $completeRoute = route('buyer.subscription.razorpay.complete');
        $razorpayKey = $key;
        $orderId = $order['id'];

        return view('Template::buyer.subscription.checkout', compact(
            'pageTitle',
            'plan',
            'razorpayKey',
            'completeRoute',
            'orderId',
            'amountPaise',
            'currency',
            'buyer'
        ));
    }

    public function completeRazorpay(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        if (! SafeSchema::subscriptionPaymentsAvailable()) {
            $notify[] = ['error', __('Subscription payments are not available.')];

            return back()->withNotify($notify);
        }

        $orderId = $request->input('razorpay_order_id');
        if (! is_string($orderId) || $orderId === '') {
            $notify[] = ['error', __('Payment could not be verified (missing order).')];

            return back()->withNotify($notify);
        }

        $buyerId = (int) auth()->guard('buyer')->id();

        $payment = SubscriptionPayment::query()
            ->where('payer_type', SubscriptionPayment::PAYER_BUYER)
            ->where('buyer_id', $buyerId)
            ->where('order_id', $orderId)
            ->where('status', SubscriptionPayment::STATUS_PENDING)
            ->first();

        if (! $payment) {
            $notify[] = ['error', __('Invalid or expired payment session.')];

            return back()->withNotify($notify);
        }

        $razorpay = app(RazorpaySubscriptionService::class);

        try {
            $razorpay->assertPaymentLegit($payment, $request->razorpay_payment_id, $request->razorpay_signature);
        } catch (SignatureVerificationError|\RuntimeException $e) {
            $payment->update([
                'status' => SubscriptionPayment::STATUS_FAILED,
                'failure_reason' => $e->getMessage(),
            ]);
            $notify[] = ['error', __('Payment verification failed.')];

            return back()->withNotify($notify);
        }

        $paymentId = $payment->id;

        try {
            DB::transaction(function () use ($paymentId, $request) {
                $locked = SubscriptionPayment::query()->whereKey($paymentId)->lockForUpdate()->first();
                if (! $locked || $locked->status !== SubscriptionPayment::STATUS_PENDING) {
                    return;
                }
                $locked->update([
                    'status' => SubscriptionPayment::STATUS_COMPLETED,
                    'payment_id' => $request->razorpay_payment_id,
                ]);
                app(SubscriptionService::class)->switchBuyerPlan((int) $locked->buyer_id, (int) $locked->plan_id);
            });
        } catch (\Throwable $e) {
            $notify[] = ['error', $e->getMessage()];

            return back()->withNotify($notify);
        }

        $payment = SubscriptionPayment::query()->findOrFail($paymentId);

        if ($payment->status !== SubscriptionPayment::STATUS_COMPLETED) {
            $notify[] = ['info', __('This payment was already processed.')];

            return to_route('buyer.home')->withNotify($notify);
        }

        $notify[] = ['success', __('Plan updated after successful payment.')];

        return to_route('buyer.home')->withNotify($notify);
    }
}
