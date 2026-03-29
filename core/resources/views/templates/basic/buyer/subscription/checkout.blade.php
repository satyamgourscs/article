@extends($activeTemplate . 'layouts.buyer_master')
@section('content')
    <div class="container py-4">
        <h5 class="mb-3">{{ __($pageTitle) }}</h5>
        <div class="card custom--card border">
            <div class="card-body">
                <p class="mb-2">@lang('Plan'): <strong>{{ __($plan->name) }}</strong></p>
                <p class="text-muted small mb-3">
                    @lang('Amount'):
                    {{ number_format($amountPaise / 100, 2) }} {{ $currency }}
                </p>
                <p class="small text-muted mb-3">@lang('The Razorpay window should open automatically. Complete payment to activate your plan.')</p>
                <form id="rzp-verify-form" action="{{ $completeRoute }}" method="post">
                    @csrf
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="">
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id" value="">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature" value="">
                </form>
                <a href="{{ route('buyer.subscription.plans') }}" class="btn btn--dark btn--sm">@lang('Cancel')</a>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        (function () {
            var options = {
                key: @json($razorpayKey),
                amount: @json($amountPaise),
                currency: @json($currency),
                name: @json(config('app.name')),
                description: @json(__('Subscription') . ': ' . $plan->name),
                order_id: @json($orderId),
                handler: function (response) {
                    document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_signature').value = response.razorpay_signature;
                    document.getElementById('rzp-verify-form').submit();
                },
                prefill: {
                    name: @json(trim(($buyer->firstname ?? '') . ' ' . ($buyer->lastname ?? ''))),
                    email: @json($buyer->email ?? ''),
                    contact: @json($buyer->mobileNumber ?? '')
                },
                theme: { color: '#2ecc71' }
            };
            var rzp = new Razorpay(options);
            rzp.on('payment.failed', function () {
                alert(@json(__('Payment failed. You can close this and try again.')));
            });
            rzp.open();
        })();
    </script>
@endpush
