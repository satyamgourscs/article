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

                                @php
                                    $locStateVal = old('state', $user->state ?? '');
                                    $locCityVal = old('city', $user->city ?? '');
                                    $locZipVal = old('zip', $user->zip ?? '');
                                @endphp
                                <h6 class="mt-4 mb-2">@lang('Location')</h6>
                                <p class="text-muted small mb-2">@lang('India only. Search state and city, or enter a 6-digit pincode to auto-fill.')</p>
                                <div class="row">
                                    <div class="form-group col-sm-12">
                                        <label class="form-label">@lang('Address')</label>
                                        <input type="text" class="form-control form--control" name="address"
                                            value="{{ old('address', $user->address) }}">
                                    </div>
                                    @include('Template::partials.india_location_form_fields', ['user' => $user])
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
                                <div class="form-group mt-4">
                                    <label class="form--label">@lang('CV / Resume (PDF, optional)')</label>
                                    <input type="file" name="cv" class="form-control form--control" accept=".pdf,application/pdf">
                                    <small class="text-muted">@lang('Max 5 MB. Leave empty to keep current file. Stored securely for firms when you apply.')</small>
                                    @if ($user->cv_public_url)
                                        <p class="small mt-2 mb-0">@lang('Current CV'):
                                            <a href="{{ $user->cv_public_url }}" target="_blank" rel="noopener noreferrer"><strong>@lang('View')</strong></a>
                                        </p>
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
    <script src="{{ asset('assets/global/js/india-location-api-select2.js') }}"></script>
@endpush
@push('style-lib')
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script>
        (function($) {
            'use strict';
            $(function() {
                initIndiaLocationApiSelect2({
                    stateSelect: '#state',
                    citySelect: '#city',
                    pincodeInput: '#pincode',
                    stateDropdownParent: $('#state').closest('.position-relative'),
                    cityDropdownParent: $('#city').closest('.position-relative'),
                    initialState: @json($locStateVal),
                    initialCity: @json($locCityVal),
                    initialPincode: @json($locZipVal),
                    placeholderState: @json(__('Search or select state')),
                    placeholderCity: @json(__('Search or select city'))
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
