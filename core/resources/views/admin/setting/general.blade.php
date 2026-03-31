@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required
                                        value="{{ gs('site_name') }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" type="text" name="cur_text" required
                                        value="{{ gs('cur_text') }}">
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" type="text" name="cur_sym" required
                                        value="{{ gs('cur_sym') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Timezone')</label>
                                <select class="select2 form-control" name="timezone">
                                    @foreach ($timezones as $key => $timezone)
                                        <option value="{{ @$key }}" @selected(@$key == $currentTimezone)>{{ __($timezone) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ gs('base_color') }}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color"
                                        value="{{ gs('base_color') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-4 col-sm-6">
                                <label class="required"> @lang('Site Secondary Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker"
                                            value="{{ gs('secondary_color') }}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="secondary_color"
                                        value="{{ gs('secondary_color') }}">
                                </div>
                            </div>
                            @if (\Illuminate\Support\Facades\Schema::hasColumn((new \App\Models\GeneralSetting)->getTable(), 'breadcrumb_image'))
                                <div class="col-12">
                                    <h6 class="mb-3 mt-2 border-top pt-3">@lang('Public site — page breadcrumb banner')</h6>
                                </div>
                                <div class="col-xl-6 col-sm-12">
                                    <div class="form-group">
                                        <label>@lang('Breadcrumb background image')</label>
                                        <input class="form-control" type="file" name="breadcrumb_image" accept=".jpg,.jpeg,.png,image/*">
                                        <p class="text-muted small mb-0">@lang('Shown behind inner page titles. Recommended: 1920×205. Overlay uses your site base color.')</p>
                                        @if (! empty(gs('breadcrumb_image')))
                                            <div class="mt-2">
                                                <img class="rounded border" style="max-height: 80px; width: 100%; max-width: 400px; object-fit: cover;"
                                                    src="{{ getImage(getFilePath('breadcrumb') . '/' . gs('breadcrumb_image'), getFileSize('breadcrumb')) }}"
                                                    alt="">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="form-group col-xl-4 col-sm-6">
                                <label> @lang('Record to Display Per page')</label>
                                <select class="select2 form-control" name="paginate_number"
                                    data-minimum-results-for-search="-1">
                                    <option value="20" @selected(gs('paginate_number') == 20)>@lang('20 items per page')</option>
                                    <option value="50" @selected(gs('paginate_number') == 50)>@lang('50 items per page')</option>
                                    <option value="100" @selected(gs('paginate_number') == 100)>@lang('100 items per page')</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-4 col-sm-6 ">
                                <label class="required"> @lang('Currency Showing Format')</label>
                                <select class="select2 form-control" name="currency_format"
                                    data-minimum-results-for-search="-1">
                                    <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>@lang('Show Currency Text and Symbol Both')</option>
                                    <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>@lang('Show Currency Text Only')</option>
                                    <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>@lang('Show Currency Symbol Only')</option>
                                </select>
                            </div>

                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Fixed Service Charge')</label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="fixed_service_charge" step="any" required value="{{ getAmount(gs('fixed_service_charge')) }}">
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>
                            @if (\Illuminate\Support\Facades\Schema::hasColumn((new \App\Models\GeneralSetting)->getTable(), 'referral_signup_bonus'))
                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Student referral bonus')</label>
                                    <p class="text-muted small">@lang('Amount credited to the referrer’s wallet when a new student signs up with their code (0 to disable).')</p>
                                    <div class="input-group">
                                        <input class="form-control" type="text" name="referral_signup_bonus" step="any"
                                            value="{{ getAmount(gs('referral_signup_bonus') ?? config('referral.signup_bonus_amount', 50)) }}">
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if (\Illuminate\Support\Facades\Schema::hasColumn((new \App\Models\GeneralSetting)->getTable(), 'referral_image')
                                || \Illuminate\Support\Facades\Schema::hasColumn((new \App\Models\GeneralSetting)->getTable(), 'referral_description'))
                                <div class="col-12">
                                    <h6 class="mb-3 mt-2 border-top pt-3">@lang('Student dashboard — Refer & Earn block')</h6>
                                </div>
                                @if (\Illuminate\Support\Facades\Schema::hasColumn((new \App\Models\GeneralSetting)->getTable(), 'referral_image'))
                                    <div class="col-xl-6 col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Referral promo image')</label>
                                            <input class="form-control" type="file" name="referral_image" accept=".jpg,.jpeg,.png,image/*">
                                            <p class="text-muted small mb-0">@lang('Shown on the student dashboard referral card. Recommended: 640×360.')</p>
                                            @if (! empty(gs('referral_image')))
                                                <div class="mt-2">
                                                    <img class="rounded border" style="max-height: 120px;"
                                                        src="{{ getImage(getFilePath('referral') . '/' . gs('referral_image'), getFileSize('referral')) }}"
                                                        alt="">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                @if (\Illuminate\Support\Facades\Schema::hasColumn((new \App\Models\GeneralSetting)->getTable(), 'referral_description'))
                                    <div class="col-xl-6 col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Referral promo description')</label>
                                            <textarea class="form-control" name="referral_description" rows="5"
                                                placeholder="@lang('Short message above the referral code on the student dashboard.')">{{ old('referral_description', gs('referral_description')) }}</textarea>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-lib')
    <script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";


            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function(color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function() {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });
        })(jQuery);
    </script>
@endpush
