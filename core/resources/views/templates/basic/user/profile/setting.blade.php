@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-main-section">
        <div class="container-fluid px-0">
            <div class="row gy-4">
                <div class="col-xl-8 col-lg-12">
                    <div class="profile-bio">
                        <div class="profile-bio__item">
                            @include('Template::user.profile.top')
                            <form class="register" method="post" action="{{ route('user.store.profile.setting') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row gy-4 justify-content-center">
                                    <div class="form-group col-md-4">
                                        <label class="form-label">@lang('Profile photo')</label>
                                        <x-image-uploader image="{{ $user->image }}" class="w-100" type="userProfile" :required="false" />
                                    </div>
                                    <div class="form-group col-md-8">
                                        <div class="row gy-3">
                                            <div class="col-sm-6 form-group">
                                                <label class="form-label">@lang('First name') <span class="text--danger">*</span></label>
                                                <input type="text" class="form-control form--control" name="firstname"
                                                    value="{{ old('firstname', $user->firstname) }}" required>
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label class="form-label">@lang('Last name') <span class="text--danger">*</span></label>
                                                <input type="text" class="form-control form--control" name="lastname"
                                                    value="{{ old('lastname', $user->lastname) }}" required>
                                            </div>
                                            <div class="form-group col-sm-12">
                                                <label class="form-label">@lang('Email')</label>
                                                <input class="form-control form--control" value="{{ $user->email }}" readonly>
                                            </div>
                                            <div class="form-group col-sm-12">
                                                <label class="form-label">@lang('Mobile number') <span class="text--danger">*</span></label>
                                                <input type="text" class="form-control form--control" name="mobile"
                                                    value="{{ old('mobile', $user->mobile) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form--label">@lang('Short bio')</label>
                                    <textarea name="short_bio" class="form-control form--control" rows="4"
                                        maxlength="5000"
                                        placeholder="@lang('A brief introduction for firms (optional)')">{{ old('short_bio', $user->about) }}</textarea>
                                </div>

                                <div class="row gy-3">
                                    <div class="form-group col-md-6">
                                        <label class="form--label">@lang('Category') <span class="text--danger">*</span></label>
                                        <select name="qualification" class="form-select form--control" required>
                                            <option value="">@lang('Select')</option>
                                            @foreach ($qualifications as $key => $label)
                                                <option value="{{ $key }}" @selected(old('qualification', $user->studentProfile?->qualification ?? '') == $key)>
                                                    {{ __($label) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form--label">@lang('Study status') <span class="text--danger">*</span></label>
                                        <select name="education_status" class="form-select form--control" required>
                                            <option value="">@lang('Select')</option>
                                            @foreach ($educationStatuses as $key => $label)
                                                <option value="{{ $key }}" @selected(old('education_status', $user->studentProfile?->education_status ?? '') == $key)>
                                                    {{ __($label) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <h6 class="mt-4 mb-2">@lang('Location')</h6>
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label class="form-label">@lang('Address')</label>
                                        <input type="text" class="form-control form--control" name="address"
                                            value="{{ old('address', $user->address) }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('State')</label>
                                        <input type="text" class="form-control form--control" name="state"
                                            value="{{ old('state', $user->state) }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('City')</label>
                                        <input type="text" class="form-control form--control" name="city"
                                            value="{{ old('city', $user->city) }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('Zip code')</label>
                                        <input type="text" class="form-control form--control" name="zip"
                                            value="{{ old('zip', $user->zip) }}">
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="form-label">@lang('Country')</label>
                                        <input class="form-control form--control" value="{{ $user->country_name }}" disabled>
                                    </div>
                                </div>

                                <h6 class="mt-4 mb-2">@lang('Interested domains (optional)')</h6>
                                <p class="text-muted small">@lang('Areas you want to work in — helps match you to roles.')</p>
                                <div class="row gy-2">
                                    @php
                                        $selectedDomains = old('preferred_domains', $user->studentProfile?->preferred_domains ?? []);
                                        if (! is_array($selectedDomains)) {
                                            $selectedDomains = [];
                                        }
                                    @endphp
                                    @foreach ($domains as $key => $label)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="preferred_domains[]"
                                                    value="{{ $key }}" id="set_domain_{{ $key }}"
                                                    @checked(in_array($key, $selectedDomains, true))>
                                                <label class="form-check-label" for="set_domain_{{ $key }}">{{ __($label) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @php
                                    $preferredStateVal = old('preferred_state', $user->studentProfile?->preferred_state ?? '');
                                    $preferredCityVal = old('preferred_city', $user->studentProfile?->preferred_city ?? '');
                                @endphp
                                <div class="row gy-3 mt-1">
                                    <div class="form-group col-md-6">
                                        <label class="form--label">@lang('Preferred state')</label>
                                        <div class="position-relative">
                                            <select name="preferred_state" id="profilePreferredState"
                                                class="form-select form--control select2-india-pref-state">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="form--label">@lang('Preferred city')</label>
                                        <div class="position-relative">
                                            <select name="preferred_city" id="profilePreferredCity"
                                                class="form-select form--control select2-india-pref-city">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <label class="form--label">@lang('Resume (PDF, optional)')</label>
                                    <input type="file" name="resume" class="form-control form--control" accept=".pdf,application/pdf">
                                    <small class="text-muted">@lang('Max 5 MB. Leave empty to keep current file.')</small>
                                    @if ($user->studentProfile && $user->studentProfile->resume_path)
                                        <p class="small mt-2 mb-0">@lang('Current'): <strong>{{ basename($user->studentProfile->resume_path) }}</strong></p>
                                    @endif
                                </div>

                                <div class="btn-wrapper mt-4">
                                    <a href="{{ route('user.home') }}" class="btn btn-outline--dark">
                                        <i class="las la-angle-double-left"></i> @lang('Back')
                                    </a>
                                    <button type="submit" class="btn btn--dark">@lang('Next: Skills') <i class="las la-angle-double-right"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-12">
                    @include('Template::user.profile.info')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/india-preferred-location-select2.js') }}"></script>
@endpush
@push('style-lib')
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script>
        window.INDIA_STATE_CITY_MAP = @json(config('india_locations'));
    </script>
    <script>
        (function($) {
            'use strict';
            $(function() {
                initIndiaPreferredLocationSelect2({
                    stateSelect: '#profilePreferredState',
                    citySelect: '#profilePreferredCity',
                    stateDropdownParent: $('#profilePreferredState').closest('.position-relative'),
                    cityDropdownParent: $('#profilePreferredCity').closest('.position-relative'),
                    initialState: @json($preferredStateVal),
                    initialCity: @json($preferredCityVal),
                    placeholderState: @json(__('Search or select state (optional)')),
                    placeholderCity: @json(__('Search or select city (optional)'))
                });
            });
        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            min-height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            border: 1px solid var(--border-color, #ced4da);
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
    </style>
@endpush
