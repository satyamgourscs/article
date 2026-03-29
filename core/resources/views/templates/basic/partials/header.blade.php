@php
    use App\Models\Language;
    $languages = Language::get();
    $defaultLang = $languages->firstWhere('is_default', Status::YES);
    $currentLangCode = session('lang', config('app.locale'));
    $currentLang = $languages->firstWhere('code', $currentLangCode) ?: $defaultLang;
    $isStudent = auth()->guard('web')->check();
    $isFirm = auth()->guard('buyer')->check();
@endphp

<header class="header" id="header">
    <div class="container">
        <nav class="navbar navbar-expand-xl navbar-light align-items-center">
            <a class="navbar-brand logo" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt=""></a>
            <div class="d-xl-none d-block job-link">
                @if ($isFirm)
                    <a href="{{ route('firm.post_job') }}" class="btn btn--base btn--sm">
                        @lang('Post job')
                    </a>
                @endif
            </div>

            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse justify-content-xl-center" id="navbarSupportedContent">
                <ul class="navbar-nav nav-menu header-main-menu align-items-xl-center">
                    <li class="nav-item {{ menuActive('home') }}">
                        <a class="nav-link" aria-current="page" href="{{ route('home') }}">@lang('Home')</a>
                    </li>
                    @if (count($pages) > 1)
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                @lang('Pages') <span class="nav-item__icon"><i class="las la-angle-down"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($pages as $k => $data)
                                    <li class="dropdown-menu__list {{ menuActive('pages', null, @$data->slug) }}">
                                        <a class="dropdown-item dropdown-menu__link"
                                            href="{{ route('pages', $data->slug) }}">
                                            {{ __($data->name) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        @foreach ($pages as $k => $data)
                            <li class="nav-item {{ menuActive('pages', null, @$data->slug) }}">
                                <a class="nav-link" href="{{ route('pages', $data->slug) }}">{{ __($data->name) }}</a>
                            </li>
                        @endforeach
                    @endif

                    @if ($isStudent)
                        <li
                            class="nav-item {{ menuActive(['jobs.portal.index', 'jobs.portal.show', 'portal.my_applications']) }}">
                            <a class="nav-link" href="{{ route('jobs.portal.index') }}">@lang('Jobs')</a>
                        </li>
                        <li class="nav-item {{ menuActive(['all.freelancers', 'talent.explore']) }}">
                            <a class="nav-link" href="{{ route('all.freelancers') }}">@lang('Students')</a>
                        </li>
                        <li class="nav-item {{ menuActive('user.subscription.*') }}">
                            <a class="nav-link" href="{{ route('user.subscription.plans') }}">@lang('Subscription')</a>
                        </li>
                    @elseif ($isFirm)
                        <li class="nav-item {{ menuActive(['all.freelancers', 'talent.explore']) }}">
                            <a class="nav-link" href="{{ route('all.freelancers') }}">@lang('Find students')</a>
                        </li>
                        <li class="nav-item {{ menuActive(['firm.post_job*', 'firm.posted_jobs.*']) }}">
                            <a class="nav-link" href="{{ route('firm.posted_jobs.index') }}">@lang('My jobs')</a>
                        </li>
                    @else
                        <li
                            class="nav-item {{ menuActive(['jobs.portal.index', 'jobs.portal.show']) }}">
                            <a class="nav-link" href="{{ route('jobs.portal.index') }}">@lang('Jobs')</a>
                        </li>
                        <li class="nav-item {{ menuActive(['all.freelancers', 'talent.explore']) }}">
                            <a class="nav-link" href="{{ route('all.freelancers') }}">@lang('Students')</a>
                        </li>
                        <li class="nav-item {{ menuActive('user.login') }}">
                            <a class="nav-link" href="{{ route('user.login') }}">@lang('Subscription')</a>
                        </li>
                    @endif

                    <li class="nav-item {{ menuActive('contact') }}">
                        <a class="nav-link" href="{{ route('contact') }}">@lang('Contact')</a>
                    </li>
                    <li class="nav-item d-flex justify-content-between w-100 d-xl-none">
                        <div class="top-button w-100">
                            <ul class="login-registration-list d-flex flex-wrap gap-2 align-items-center">
                                @if ($isStudent)
                                    <li class="login-registration-list__item">
                                        <a href="{{ route('student.dashboard') }}" class="login-registration-list__link">@lang('Dashboard')</a>
                                    </li>
                                @elseif ($isFirm)
                                    <li class="login-registration-list__item">
                                        <a href="{{ route('firm.dashboard') }}" class="login-registration-list__link">@lang('Dashboard')</a>
                                    </li>
                                @else
                                    <li class="login-registration-list__item">
                                        <a href="{{ route('signup.student') }}"
                                            class="login-registration-list__link">@lang('Student signup')</a>
                                    </li>
                                    <li class="login-registration-list__item">
                                        <a href="{{ route('signup.company') }}"
                                            class="login-registration-list__link">@lang('Company signup')</a>
                                    </li>
                                    <li class="login-registration-list__item">
                                        <button type="button"
                                            class="login-registration-list__link btn btn-link p-0 border-0"
                                            data-bs-toggle="modal" data-bs-target="#otpAuthModal">@lang('Login')</button>
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </li>
                </ul>
            </div>
            <div class="d-xl-block d-none">
                <div class="top-button d-flex flex-wrap justify-content-between align-items-center">

                    <ul class="login-registration-list d-flex flex-wrap justify-content-between align-items-center">
                        @if ($isStudent)
                            <li class="login-registration-list__item">
                                <a href="{{ route('student.dashboard') }}" class="login-registration-list__link">@lang('Dashboard')</a>
                            </li>
                        @elseif ($isFirm)
                            <li class="login-registration-list__item">
                                <a href="{{ route('firm.dashboard') }}" class="login-registration-list__link">@lang('Dashboard')</a>
                            </li>
                        @else
                            <li class="login-registration-list__item">
                                <a href="{{ route('signup.student') }}" class="btn btn--base btn--sm">
                                    @lang('Student signup')
                                </a>
                            </li>
                            <li class="login-registration-list__item ms-1">
                                <a href="{{ route('signup.company') }}" class="btn btn-outline--base btn--sm">
                                    @lang('Company signup')
                                </a>
                            </li>
                            <li class="login-registration-list__item ms-1">
                                <button type="button" class="btn btn-outline--secondary btn--sm" data-bs-toggle="modal"
                                    data-bs-target="#otpAuthModal">
                                    @lang('Login')
                                </button>
                            </li>
                        @endif

                        @if ($isFirm)
                            <li class="login-registration-list__item ms-2">
                                <a href="{{ route('firm.post_job') }}" class="btn btn--base btn--sm">@lang('Post job')</a>
                            </li>
                        @endif
                    </ul>

                </div>
            </div>
        </nav>
    </div>
</header>
@include('Template::partials.otp_signup_modal')
