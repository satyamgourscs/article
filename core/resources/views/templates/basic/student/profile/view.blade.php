@extends('Template::layouts.buyer_master')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('buyer.firm.posted_jobs.index') }}"
            class="btn btn--sm btn-outline--base">← @lang('Back')</a>
    </div>

    <div class="card custom--card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 align-items-start">
                <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->image, avatar: true) }}" alt=""
                    class="rounded" style="width: 96px; height: 96px; object-fit: cover;">
                <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $user->fullname }}</h5>
                    @if ($user->username)
                        <p class="text-muted small mb-1">{{ '@'.$user->username }}</p>
                    @endif
                    @if ($user->tagline)
                        <p class="mb-2">{{ $user->tagline }}</p>
                    @endif
                    <ul class="list-unstyled small mb-0">
                        @if (filled($user->email))
                            <li><strong>@lang('Email'):</strong> {{ $user->email }}</li>
                        @endif
                        @if (filled($user->mobile))
                            <li><strong>@lang('Mobile'):</strong> {{ $user->mobileNumber }}</li>
                        @endif
                        @if ($user->city || $user->state)
                            <li><strong>@lang('Location'):</strong>
                                {{ trim(implode(', ', array_filter([$user->city, $user->state, $user->country_name]))) }}
                            </li>
                        @endif
                    </ul>
                    @if ($user->cv_public_url)
                        <a href="{{ $user->cv_public_url }}" target="_blank" rel="noopener noreferrer" class="btn btn--sm btn--base mt-3">
                            @lang('View / download CV')
                        </a>
                    @endif
                </div>
            </div>
            @if ($user->about)
                <hr>
                <h6>@lang('About')</h6>
                <div class="text-muted">{!! nl2br(e($user->about)) !!}</div>
            @endif
        </div>
    </div>

    @if ($user->skills->isNotEmpty())
        <h6 class="mb-2">@lang('Skills')</h6>
        <div class="mb-4">
            @foreach ($user->skills as $skill)
                <span class="badge bg--secondary me-1 mb-1">{{ __($skill->name) }}</span>
            @endforeach
        </div>
    @endif

    @if ($user->educations->isNotEmpty())
        <h6 class="mb-2">@lang('Education')</h6>
        <ul class="mb-4">
            @foreach ($user->educations as $ed)
                <li>{{ $ed->school ?? __('Education entry') }}</li>
            @endforeach
        </ul>
    @endif

    @if ($user->portfolios->isNotEmpty())
        <h6 class="mb-2">@lang('Portfolio')</h6>
        <div class="row g-3">
            @foreach ($user->portfolios as $portfolio)
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        @if ($portfolio->image)
                            <img src="{{ getImage(getFilePath('portfolio') . '/' . $portfolio->image, getFileSize('portfolio')) }}"
                                alt="" class="img-fluid rounded mb-2 w-100" style="max-height: 160px; object-fit: cover;">
                        @endif
                        <strong>{{ $portfolio->title }}</strong>
                        @if ($portfolio->description)
                            <p class="small text-muted mb-1 mt-1">{{ strLimit($portfolio->description, 200) }}</p>
                        @endif
                        @if ($portfolio->url)
                            <a href="{{ $portfolio->url }}" target="_blank" rel="noopener noreferrer" class="small">@lang('Link')</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
