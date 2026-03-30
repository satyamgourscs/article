@php
    // Direct database query to ensure we get the latest data
    $accountRaw = \App\Models\Frontend::where('tempname', 'basic')
        ->where('data_keys', 'account.content')
        ->orderBy('id', 'desc')
        ->first();
    
    $account = null;
    if ($accountRaw && $accountRaw->data_values) {
        // Handle different data types
        if (is_object($accountRaw->data_values)) {
            $account = $accountRaw->data_values;
        } elseif (is_string($accountRaw->data_values)) {
            $decoded = json_decode($accountRaw->data_values);
            $account = is_object($decoded) ? $decoded : null;
        } elseif (is_array($accountRaw->data_values)) {
            $account = (object)$accountRaw->data_values;
        }
    }
    
    // Fallback values if database query fails
    if (!$account || !is_object($account)) {
        $account = (object)[
            'freelancer_title' => __('Sign Up as a CA Student'),
            'freelancer_content' => __('Start your CA journey, connect with firms, and gain real articleship experience.'),
            'freelancer_button_name' => __('CA Student signup'),
            'buyer_title' => __('Sign Up as a CA Firm'),
            'buyer_content' => __('Post articleship opportunities, hire CA students, and grow your firm.'),
            'buyer_button_name' => __('CA Firm signup'),
            'freelancer' => null,
            'buyer' => null
        ];
    }
@endphp

<div class="account-section my-120">
    <div class="container">
        @if (! auth()->guard('web')->check() && ! auth()->guard('buyer')->check())
            <div class="row gy-4">
                <div class="col-xl-6">
                    <div class="account-item">
                        <div class="account-item__content highlight">
                            <h3 class="account-item__title s-highlight" data-s-break="-1" data-s-length="1">{{ @$account->freelancer_title ?? __('Sign Up as a CA Student') }}</h3>
                            <p class="account-item__text">{{ @$account->freelancer_content ?? __('Start your CA journey, connect with firms, and gain real articleship experience.') }}</p>
                            <div class="account-item__btn">
                                <a href="{{ route('signup.student') }}" class="btn btn--base">{{ @$account->freelancer_button_name ?? __('CA Student signup') }}</a>
                            </div>
                        </div>
                        <div class="account-item__thumb">
                            <img src="{{ frontendImage('account', @$account->freelancer, '530x490') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="account-item">
                        <div class="account-item__content highlight">
                            <h3 class="account-item__title s-highlight" data-s-break="-1" data-s-length="1">{{ @$account->buyer_title ?? __('Sign Up as a CA Firm') }}</h3>
                            <p class="account-item__text">{{ @$account->buyer_content ?? __('Post articleship opportunities, hire CA students, and grow your firm.') }}</p>
                            <div class="account-item__btn">
                                <a href="{{ route('signup.company') }}" class="btn btn--base">{{ @$account->buyer_button_name ?? __('CA Firm signup') }}</a>
                            </div>
                        </div>
                        <div class="account-item__thumb">
                            <img src="{{ frontendImage('account', @$account->buyer, '750x530') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row gy-4 justify-content-center">
                <div class="col-xl-8">
                    <div class="account-item">
                        <div class="account-item__content highlight text-center py-4">
                            @auth('web')
                                <h3 class="account-item__title">@lang('Student Dashboard')</h3>
                            @elseauth('buyer')
                                <h3 class="account-item__title">@lang('Company Dashboard')</h3>
                            @endauth
                            <p class="account-item__text">@lang('Article Connect tagline')</p>
                            <div class="account-item__btn d-flex flex-wrap gap-2 justify-content-center">
                                @auth('web')
                                    <a href="{{ route('user.home') }}" class="btn btn--base">@lang('Student Dashboard')</a>
                                @elseauth('buyer')
                                    <a href="{{ route('buyer.home') }}" class="btn btn--base">@lang('Company Dashboard')</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
