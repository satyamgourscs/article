@extends('Template::layouts.frontend')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <h4 class="mb-3">@lang('View limit reached')</h4>
                        <p class="text-muted mb-3">{{ $viewCheck['message'] ?? '' }}</p>
                        @if (isset($job))
                            <p class="small mb-4">@lang('Opportunity'): <strong>{{ __($job->title) }}</strong></p>
                        @endif
                        <a href="{{ route('user.subscription.plans') }}" class="btn btn--base">@lang('Upgrade Plan')</a>
                        <a href="{{ route('freelance.jobs') }}" class="btn btn-outline--base ms-2">@lang('Back to listings')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
