@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-main-section">
        <div class="container-fluid px-0">
            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="profile-bio">
                        <div class="profile-bio__item">
                            @include('Template::user.profile.top')
                            <p class="text-muted small mb-3">
                                @lang('All fields are optional. Before withdrawing, add your UPI ID and/or bank account number with IFSC.')
                            </p>
                            <form action="{{ route('user.store.profile.bank') }}" method="POST">
                                @csrf
                                <div class="row gy-3">
                                    <div class="col-md-12">
                                        <label class="form-label">@lang('Bank name')</label>
                                        <input type="text" name="bank_name" class="form-control form--control"
                                            value="{{ old('bank_name', $user->bank_name) }}" maxlength="191"
                                            autocomplete="organization">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">@lang('Account number')</label>
                                        <input type="text" name="bank_account_number" class="form-control form--control"
                                            value="{{ old('bank_account_number', $user->bank_account_number) }}"
                                            maxlength="64" autocomplete="off">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">@lang('IFSC')</label>
                                        <input type="text" name="bank_ifsc" class="form-control form--control"
                                            value="{{ old('bank_ifsc', $user->bank_ifsc) }}" maxlength="32"
                                            style="text-transform:uppercase" autocomplete="off">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">@lang('Account holder name')</label>
                                        <input type="text" name="bank_account_holder_name"
                                            class="form-control form--control"
                                            value="{{ old('bank_account_holder_name', $user->bank_account_holder_name) }}"
                                            maxlength="191" autocomplete="name">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">@lang('UPI ID')</label>
                                        <input type="text" name="upi_id" class="form-control form--control"
                                            value="{{ old('upi_id', $user->upi_id) }}" maxlength="255"
                                            placeholder="@lang('e.g. name@upi')" autocomplete="off">
                                    </div>
                                </div>
                                <div class="btn-wrapper mt-4">
                                    <a href="{{ route('user.profile.education') }}" class="btn btn-outline--dark">
                                        <i class="las la-angle-double-left"></i> @lang('Previous')
                                    </a>
                                    <button type="submit" class="btn btn--dark">@lang('Save')
                                        <i class="las la-check"></i></button>
                                    <a href="{{ route('user.profile.portfolio') }}" class="btn btn-outline--base ms-1">
                                        @lang('Continue to work samples')
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @include('Template::user.profile.info')
                </div>
            </div>
        </div>
    </div>
@endsection
