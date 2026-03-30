
    @php
        $banner = getContent('banner.content', true)->data_values;
        $clientElement = getContent('client.element', false, null, true);
    @endphp

    <section class="banner-section">
        <div class="banner-section__shape">
            <img src="{{ frontendImage('banner', @$banner->shape, '475x630') }}" alt="">
        </div>
        <div class="container">
            <div class="row gy-5 align-items-start">
                <div class="col-lg-6">
                    <div class="banner-content highlight">
                        <h1 class="banner-content__title s-highlight" data-s-break="-1" data-s-length="1">
                            {{ @$banner->heading ?? __('Find the Best Articleship Opportunities') }}</h1>
                        <p class="banner-content__desc">{{ @$banner->subheading ?? __('Connecting CA Students with CA Firms') }}</p>
                        @if (! auth()->guard('web')->check() && ! auth()->guard('buyer')->check())
                            <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                                <a href="{{ route('signup.student') }}" class="btn btn--base btn--sm">@lang('CA Student signup')</a>
                                <a href="{{ route('signup.company') }}" class="btn btn-outline--base btn--sm">@lang('CA Firm signup')</a>
                            </div>
                        @endif
                        @auth('web')
                            <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                                <a href="{{ route('user.home') }}" class="btn btn--base btn--sm">@lang('Student Dashboard')</a>
                            </div>
                        @endauth
                        @auth('buyer')
                            <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                                <a href="{{ route('buyer.home') }}" class="btn btn--base btn--sm">@lang('Company Dashboard')</a>
                            </div>
                        @endauth
                    </div>

                    <div class="buyer-wrapper">
                        <span class="buyer-wrapper__title">{{ @$banner->subtitle ?? __('Article Connect tagline') }}</span>
                        <div class="brand-slider">
                            @foreach ($clientElement as $client)
                                <img src= "{{ frontendImage('client', @$client->data_values->image, '290x100') }}"
                                    alt="">
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-xsm-block d-none">
                    <div class="banner-thumb-wrapper">
                        <div class="banner-thumb">
                            <img src="{{ frontendImage('banner', @$banner->image, '1140x970') }}" alt="">
                        </div>
                        <div class="banner-thumb-wrapper__content">
                            <div class="banner-thumb-wrapper__item one">
                                {{ __(@$banner->feature_one) }}
                            </div>
                            <div class="banner-thumb-wrapper__item two">
                                {{ __(@$banner->feature_two) }}
                            </div>
                            <div class="banner-thumb-wrapper__item three">
                                <span class="icon">
                                    <img src="{{ asset($activeTemplateTrue . 'shape/heart.png') }}" alt="">
                                </span>
                                <div class="content">
                                    <span class="text"> {{ __(@$banner->feature_three) }}</span>
                                    <ul class="rating-list">
                                        <li class="rating-list__item"> <i class="las la-star"></i> </li>
                                        <li class="rating-list__item"> <i class="las la-star"></i> </li>
                                        <li class="rating-list__item"> <i class="las la-star"></i> </li>
                                        <li class="rating-list__item"> <i class="las la-star"></i> </li>
                                        <li class="rating-list__item"> <i class="las la-star"></i> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="banner-thumb-shape">
                            <span class="banner-thumb-shape__one"></span>
                            <span class="banner-thumb-shape__two"></span>
                            <span class="banner-thumb-shape__three"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

