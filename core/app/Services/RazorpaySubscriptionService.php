<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpaySubscriptionService
{
    public function credentials(): array
    {
        $key = config('services.razorpay.key_id');
        $secret = config('services.razorpay.key_secret');
        if (! is_string($key) || $key === '' || ! is_string($secret) || $secret === '') {
            throw new \RuntimeException(__('Razorpay is not configured. Set RAZORPAY_KEY and RAZORPAY_SECRET in the environment.'));
        }

        return [$key, $secret];
    }

    public function currency(): string
    {
        return (string) config('services.razorpay.currency', 'INR');
    }

    /** Amount in smallest currency unit (e.g. paise for INR). */
    public function orderAmountPaise(Plan $plan): int
    {
        $paise = (int) round((float) $plan->price * 100);

        return max(100, $paise);
    }

    /**
     * Verify Razorpay signature and that the order amount matches the pending payment.
     *
     * @throws SignatureVerificationError
     */
    public function assertPaymentLegit(SubscriptionPayment $payment, string $razorpayPaymentId, string $razorpaySignature): void
    {
        [$key, $secret] = $this->credentials();
        $api = new Api($key, $secret);
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $payment->order_id,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature,
        ]);

        $order = $api->order->fetch($payment->order_id);
        if ((int) $order['amount'] !== (int) $payment->amount_paise) {
            throw new \RuntimeException('payment_amount_mismatch');
        }
    }
}
