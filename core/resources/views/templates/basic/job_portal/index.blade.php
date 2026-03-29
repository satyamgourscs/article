@extends('Template::layouts.frontend')
@section('content')
    <section class="account py-60">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="mb-0">{{ __($pageTitle) }}</h2>
                </div>
            </div>
            <div class="row gy-4">
                @forelse ($jobs as $job)
                    <div class="col-md-6 col-lg-4">
                        <div class="card custom--card h-100">
                            <div class="card-body">
                                <span class="badge badge--base mb-2">{{ __($job->type === \App\Models\PostedJob::TYPE_ARTICLESHIP ? 'Articleship' : 'Short term audit') }}</span>
                                <h5 class="card-title">{{ __($job->title) }}</h5>
                                <p class="text-muted small mb-2">
                                    @if ($job->company_name)
                                        {{ $job->company_name }}
                                    @else
                                        {{ '@'.$job->buyer->username }}
                                    @endif
                                    @if ($job->location)
                                        · {{ $job->location }}
                                    @endif
                                </p>
                                @if ($job->domain)
                                    <p class="small mb-2">@lang('Domain'): {{ $job->domain }}</p>
                                @endif
                                @if ($job->type === \App\Models\PostedJob::TYPE_ARTICLESHIP && $job->stipend)
                                    <p class="small mb-0">@lang('Stipend'): {{ showAmount($job->stipend) }}</p>
                                @elseif ($job->per_day_pay)
                                    <p class="small mb-0">@lang('Per day'): {{ showAmount($job->per_day_pay) }}</p>
                                @endif
                                <a href="{{ route('jobs.portal.show', $job) }}"
                                    class="btn btn--base btn--sm w-100 mt-3">@lang('View')</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted">@lang('No open jobs right now.')</p>
                    </div>
                @endforelse
            </div>
            @if ($jobs->hasPages())
                <div class="mt-4">
                    {{ paginateLinks($jobs) }}
                </div>
            @endif
        </div>
    </section>
@endsection
