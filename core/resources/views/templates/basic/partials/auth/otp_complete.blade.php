@php
    $prefill = $prefill ?? [];
@endphp
@extends($activeTemplate . 'layouts.app')
@section('panel')
    <section class="account py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card custom--card">
                        <div class="card-body p-4">
                            <h4 class="mb-4">{{ __($pageTitle) }}</h4>
                            @if (config('otp.test_mode'))
                                <div class="alert alert-warning small mb-3" role="alert">{{ config('otp.test_banner') }}
                                </div>
                            @endif
                            <form id="otpCompleteForm" method="post" action="{{ route('auth.otp.complete') }}">
                                @csrf
                                @if ($guard === 'web')
                                    <div class="form-group mb-3">
                                        <label class="form--label">@lang('Referral code (optional)')</label>
                                        <input type="text" name="referral_code" class="form-control form--control"
                                            value="{{ old('referral_code', $prefill['referral_code'] ?? '') }}"
                                            autocomplete="off">
                                    </div>
                                @endif
                                <div class="form-group mb-3">
                                    <label class="form--label">@lang('First name')</label>
                                    <input type="text" name="firstname" class="form-control form--control" required
                                        value="{{ old('firstname', $prefill['firstname'] ?? '') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form--label">@lang('Last name')</label>
                                    <input type="text" name="lastname" class="form-control form--control" required
                                        value="{{ old('lastname', $prefill['lastname'] ?? '') }}">
                                </div>
                                <button type="submit" class="btn btn--base w-100">@lang('Create account')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        (function() {
            const form = document.getElementById('otpCompleteForm');
            if (!form) return;
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const fd = new FormData(form);
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                const data = await res.json();
                if (data.ok && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                alert(data.message || 'Unable to complete signup');
            });
        })();
    </script>
@endpush
