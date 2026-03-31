@php
    $currentStep = auth()->user()->step;
    $activeRoute = Route::currentRouteName();
    $wizardProgressPct = (int) auth()->user()->profile_completion;
@endphp

<h6 class="mb-2">@lang('Profile wizard')</h6>
<div class="progress mb-3" style="height: 6px;">
    <div class="progress-bar bg--base" role="progressbar" style="width: {{ $wizardProgressPct }}%; min-width: 2%;"
        aria-valuenow="{{ $wizardProgressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class="profile-wizard-steps d-flex flex-wrap gap-2 gap-md-3 pb-3 mb-3 border-bottom border-light">
    @php
        $stepBadge = fn ($active) => $active ? 'text-white' : 'bg-light text-dark border';
        $stepStyle = fn ($active) => $active ? 'background:hsl(var(--base));' : '';
    @endphp
    <span class="badge rounded-pill px-3 py-2 {{ $stepBadge($activeRoute === 'user.profile.setting') }}" style="{{ $stepStyle($activeRoute === 'user.profile.setting') }}">
        1 @lang('Basic info')
    </span>
    <span class="badge rounded-pill px-3 py-2 {{ $stepBadge($activeRoute === 'user.profile.skill') }}" style="{{ $stepStyle($activeRoute === 'user.profile.skill') }}">
        2 @lang('Skills')
    </span>
    <span class="badge rounded-pill px-3 py-2 {{ $stepBadge($activeRoute === 'user.profile.portfolio') }}" style="{{ $stepStyle($activeRoute === 'user.profile.portfolio') }}">
        3 @lang('Portfolio')
    </span>
    <span class="badge rounded-pill px-3 py-2 {{ $stepBadge($activeRoute === 'user.profile.education') }}" style="{{ $stepStyle($activeRoute === 'user.profile.education') }}">
        4 @lang('Education')
    </span>
    <span class="badge rounded-pill px-3 py-2 {{ $stepBadge($activeRoute === 'user.profile.bank') }}" style="{{ $stepStyle($activeRoute === 'user.profile.bank') }}">
        5 @lang('Bank')
    </span>
</div>

<p class="text-muted small mb-2">@lang('Complete these steps to finish your student profile.')</p>
<ul class="page-list pt-2">
    <li class="nav-item {{ $activeRoute === 'user.profile.setting' ? 'current' : '' }} {{ $currentStep >= 1 || $activeRoute === 'user.profile.setting' ? 'active' : '' }}">
        <a class="nav-link {{ menuActive('user.profile.setting') }}" href="{{ route('user.profile.setting') }}">
            <span class="profile-item__title">@lang('Step 1: Basic info')</span>
        </a>
    </li>
    <li class="nav-item {{ $activeRoute === 'user.profile.skill' ? 'current' : '' }} {{ $currentStep >= 2 ? 'active' : '' }}">
        <a class="nav-link {{ menuActive('user.profile.skill') }} {{ $currentStep < 1 ? 'disabled pe-none text-muted' : '' }}"
            href="{{ $currentStep >= 1 ? route('user.profile.skill') : 'javascript:void(0)' }}">
            <span class="profile-item__title">@lang('Step 2: Skills & experience')</span>
        </a>
    </li>
    <li class="nav-item {{ $activeRoute === 'user.profile.portfolio' ? 'current' : '' }} {{ $currentStep >= 4 ? 'active' : '' }}">
        <a class="nav-link {{ menuActive('user.profile.portfolio') }} {{ $currentStep < 2 ? 'disabled pe-none text-muted' : '' }}"
            href="{{ $currentStep >= 2 ? route('user.profile.portfolio') : 'javascript:void(0)' }}">
            <span class="profile-item__title">@lang('Step 3: Portfolio')</span>
        </a>
    </li>
    <li class="nav-item {{ $activeRoute === 'user.profile.education' ? 'current' : '' }} {{ $currentStep >= 4 ? 'active' : '' }}">
        <a class="nav-link {{ menuActive('user.profile.education') }} {{ $currentStep < 2 ? 'disabled pe-none text-muted' : '' }}"
            href="{{ $currentStep >= 2 ? route('user.profile.education') : 'javascript:void(0)' }}">
            <span class="profile-item__title">@lang('Step 4: Education (optional)')</span>
        </a>
    </li>
    <li class="nav-item {{ $activeRoute === 'user.profile.bank' ? 'current' : '' }}">
        <a class="nav-link {{ menuActive('user.profile.bank') }} {{ $currentStep < 2 ? 'disabled pe-none text-muted' : '' }}"
            href="{{ $currentStep >= 2 ? route('user.profile.bank') : 'javascript:void(0)' }}">
            <span class="profile-item__title">@lang('Step 5: Bank details & publish')</span>
        </a>
    </li>
</ul>
