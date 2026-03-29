@extends('Template::layouts.frontend')
@section('content')
    <section class="account py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-body p-4">
                            <p class="mb-2">
                                <span
                                    class="badge badge--base">{{ __($postedJob->type === \App\Models\PostedJob::TYPE_ARTICLESHIP ? 'Articleship' : 'Short term audit') }}</span>
                            </p>
                            <h3 class="mb-3">{{ __($postedJob->title) }}</h3>
                            @if ($postedJob->company_name)
                                <p class="mb-1"><strong>@lang('Company'):</strong> {{ $postedJob->company_name }}</p>
                            @endif
                            @if ($postedJob->location)
                                <p class="mb-1"><strong>@lang('Location'):</strong> {{ $postedJob->location }}</p>
                            @endif
                            @if ($postedJob->domain)
                                <p class="mb-1"><strong>@lang('Domain'):</strong> {{ $postedJob->domain }}</p>
                            @endif
                            @if ($postedJob->type === \App\Models\PostedJob::TYPE_ARTICLESHIP)
                                @if ($postedJob->stipend)
                                    <p class="mb-1"><strong>@lang('Stipend'):</strong> {{ showAmount($postedJob->stipend) }}</p>
                                @endif
                                @if ($postedJob->open_positions !== null)
                                    <p class="mb-1"><strong>@lang('Open positions'):</strong> {{ $postedJob->open_positions }}</p>
                                @endif
                                @if ($postedJob->current_articles !== null)
                                    <p class="mb-1"><strong>@lang('Current articles'):</strong>
                                        {{ $postedJob->current_articles }}</p>
                                @endif
                            @else
                                @if ($postedJob->per_day_pay)
                                    <p class="mb-1"><strong>@lang('Per day pay'):</strong>
                                        {{ showAmount($postedJob->per_day_pay) }}</p>
                                @endif
                                @if ($postedJob->duration)
                                    <p class="mb-1"><strong>@lang('Duration'):</strong> {{ $postedJob->duration }}</p>
                                @endif
                            @endif
                            @if ($postedJob->description)
                                <div class="mt-3">
                                    <h6>@lang('Description')</h6>
                                    <div class="article-description">{!! nl2br(e($postedJob->description)) !!}</div>
                                </div>
                            @endif

                            <div class="mt-4">
                                @auth
                                    @if ($alreadyApplied)
                                        <button class="btn btn-outline--base w-100" disabled>@lang('Applied')</button>
                                    @else
                                        <form action="{{ route('jobs.portal.apply') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="job_id" value="{{ $postedJob->id }}">
                                            <button type="submit" class="btn btn--base w-100">@lang('Apply')</button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('user.login') }}"
                                        class="btn btn--base w-100">@lang('Log in to apply')</a>
                                @endauth
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('jobs.portal.index') }}">← @lang('Back to jobs')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
