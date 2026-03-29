@extends($activeTemplate . 'layouts.buyer_master')
@section('content')
    <div class="container py-4">
        <h5 class="mb-3">{{ __($pageTitle) }}</h5>
        @if ($current && $current->plan)
            <p class="text-muted">@lang('Current plan'): <strong>{{ __($current->plan->name) }}</strong>
                — @lang('Posts used'): {{ $current->jobs_posted_count }} /
                @if ($current->plan->job_post_limit >= 999999)
                    @lang('Unlimited')
                @else
                    {{ $current->plan->job_post_limit }}
                @endif
            </p>
        @endif
        <div class="row g-3">
            @foreach ($plans as $plan)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border">
                        <div class="card-body d-flex flex-column">
                            <h6>{{ __($plan->name) }}</h6>
                            <p class="text-muted small mb-2">{{ showAmount($plan->price) }} / {{ $plan->duration_days }} @lang('days')</p>
                            <ul class="small flex-grow-1">
                                <li>@lang('Job posts'): {{ $plan->job_post_limit >= 999999 ? __('Unlimited') : $plan->job_post_limit }}</li>
                            </ul>
                            @if ($current && $current->plan_id == $plan->id)
                                <button class="btn btn--dark btn--sm w-100" disabled>@lang('Current')</button>
                            @else
                                <form action="{{ route('buyer.subscription.pay', $plan->id) }}" method="post" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn--base btn--sm w-100">@lang('Choose plan')</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="small text-muted mt-3">@lang('Paid plans use Razorpay. Free plans switch immediately.')</p>
    </div>
@endsection
