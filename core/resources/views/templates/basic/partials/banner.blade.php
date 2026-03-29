
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
                            {{ @$banner->heading ?? 'Find the Right Articleship & Internship Opportunities' }}</h1>
                        <p class="banner-content__desc">{{ @$banner->subheading ?? 'Article Connect helps students discover articleship, internship, and training opportunities with trusted firms and companies.' }}</p>
                        <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                            <a href="{{ route('signup.student') }}" class="btn btn--base btn--sm">@lang('Student signup')</a>
                            <a href="{{ route('signup.company') }}" class="btn btn-outline--base btn--sm">@lang('Company signup')</a>
                        </div>
                    </div>
                    <form class="banner-search-form" action="{{ route('jobs.portal.index') }}" method="GET">
                        <div class="search-box">
                            <input type="text" name="search"
                                placeholder="@lang('Search jobs, firms, or skills')" autocomplete="off"
                                aria-label="@lang('Search jobs, firms, or skills')">
                            <button type="submit" aria-label="@lang('Search')">
                                <i class="las la-search" aria-hidden="true"></i>
                            </button>
                        </div>
                    </form>

                    <div class="buyer-wrapper">
                        <span class="buyer-wrapper__title">{{ @$banner->subtitle ?? 'Connect with trusted CA firms, companies, and training opportunities designed for students and career starters.' }}</span>
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

