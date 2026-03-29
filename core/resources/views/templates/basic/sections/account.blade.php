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
            'freelancer_title' => 'Sign Up as a Student',
            'freelancer_content' => 'Build your profile, apply for articleship and internship opportunities, and start your professional journey.',
            'freelancer_button_name' => 'Create Student Account',
            'buyer_title' => 'Sign Up as a Firm',
            'buyer_content' => 'Post articleship and internship opportunities, connect with talented students, and build your team.',
            'buyer_button_name' => 'Create Firm Account',
            'freelancer' => null,
            'buyer' => null
        ];
    }
@endphp

<div class="account-section my-120">
    <div class="container">
        <div class="row gy-4">
            <div class="col-xl-6">
                <div class="account-item">
                    <div class="account-item__content highlight">
                        <h3 class="account-item__title s-highlight" data-s-break="-1" data-s-length="1">{{ @$account->freelancer_title ?? 'Sign Up as a Student' }}</h3>
                        <p class="account-item__text">{{ @$account->freelancer_content ?? 'Build your profile, apply for articleship and internship opportunities, and start your professional journey.' }}</p>
                        <div class="account-item__btn">
                            <a href="{{ route('signup.student') }}" class="btn btn--base">{{ @$account->freelancer_button_name ?? 'Create Student Account' }}</a>
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
                        <h3 class="account-item__title s-highlight" data-s-break="-1" data-s-length="1">{{ @$account->buyer_title ?? 'Sign Up as a Firm' }}</h3>
                        <p class="account-item__text">{{ @$account->buyer_content ?? 'Post articleship and internship opportunities, connect with talented students, and build your team.' }}</p>
                        <div class="account-item__btn">
                            <a href="{{ route('signup.company') }}" class="btn btn--base">{{ @$account->buyer_button_name ?? 'Create Firm Account' }}</a>
                        </div>
                    </div>
                    <div class="account-item__thumb">
                        <img src="{{ frontendImage('account', @$account->buyer, '750x530') }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
