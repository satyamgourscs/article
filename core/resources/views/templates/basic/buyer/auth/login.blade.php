@extends($activeTemplate . 'layouts.app')
@section('panel')
    @php
        $login = getContent('login.content', true)->data_values;
        $switchingBtn = getContent('switching_button.content', true)->data_values;
        $banner = getContent('banner.content', true)->data_values;
    @endphp

    <section class="account">
        <div class="account-inner">
            <div class="account-inner__left">
                <div class="account-inner__shape">
                    <img src="{{ frontendImage('banner', @$banner->shape, '475x630') }}" alt="">
                </div>
                <div class="account-thumb">
                    <img src="{{ frontendImage('login', @$login->image, '770x670') }}" alt="">
                </div>
            </div>
            <div class="account-inner__right">
                <div class="account-form-wrapper">
                    <a href="{{ route('home') }}" class="account-form__logo">
                        <img src="{{ siteLogo() }}" alt="">
                    </a>
                    <form method="POST" action="{{ route('buyer.login') }}" class="verify-gcaptcha loginForm">
                        @csrf
                        <div class="account-form">
                            <div class="radio-btn-wrapper">
                                <div class="form--radio">
                                    <input class="form-check-input" type="radio" name="apply-wrapper"
                                        id="apply-freelancer" value="1"
                                        @if (Route::currentRouteName() == 'user.login') checked @endif
                                        onclick="window.location='{{ route('user.login') }}'">
                                    <label class="form-check-label" for="apply-freelancer">
                                        <span class="text">{{ __($switchingBtn->freelancer_login_button) }}</span>
                                    </label>
                                </div>
                                <div class="form--radio">
                                    <input class="form-check-input" type="radio" name="apply-wrapper" id="apply-buyer"
                                        value="2" @if (Route::currentRouteName() == 'buyer.login') checked @endif
                                        onclick="window.location='{{ route('buyer.login') }}'">
                                    <label class="form-check-label" for="apply-buyer">
                                        <span class="text">{{ __($switchingBtn->buyer_login_button) }} </span>
                                    </label>
                                </div>
                            </div>
                            <p class="text"> @lang('Welcome Back') </p>
                            <h5 class="account-form__title"> {{ __(@$login->heading) }}</h5>

                            @include('Template::partials.auth.otp_login_form', ['otpGuardTarget' => 'buyer'])

                            <p class="account-form__text mt-3"> @lang('New here?')
                                <a href="{{ route('home') }}" class="text--base "> @lang('Sign up from the home page') </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
