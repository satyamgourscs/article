@extends($activeTemplate . 'layouts.app')
@section('panel')
    <section class="account py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="mb-2">@lang('Student signup')</h4>
                            <p class="text-muted small mb-4">@lang('Create your account with a one-time code. No password needed.')</p>
                            @if (config('otp.test_mode'))
                                <div class="alert alert-warning small mb-3">{{ config('otp.test_banner') }}</div>
                            @endif

                            <div id="signupStudentRoot" data-start-url="{{ route('signup.student.start') }}"
                                data-send-url="{{ route('auth.otp.send') }}" data-verify-url="{{ route('auth.otp.verify') }}"
                                data-csrf="{{ csrf_token() }}">

                                <div id="stepDetails">
                                    <h6 class="mb-3 text--base">@lang('Your details')</h6>
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('First name') <span class="text--danger">*</span></label>
                                            <input type="text" id="suStFirstname" class="form-control form--control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Last name') <span class="text--danger">*</span></label>
                                            <input type="text" id="suStLastname" class="form-control form--control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form--label">@lang('Email or mobile') <span class="text--danger">*</span></label>
                                            <input type="text" id="suStContact" class="form-control form--control" autocomplete="username" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Qualification') <span class="text--danger">*</span></label>
                                            <select id="suStQual" class="form-select form--control" required>
                                                <option value="">@lang('Select')</option>
                                                @foreach ($qualifications as $key => $label)
                                                    <option value="{{ $key }}">{{ __($label) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form--label">@lang('Preferred domain(s)') <span class="text--danger">*</span></label>
                                            <div class="row gy-2">
                                                @foreach ($domains as $key => $label)
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input su-st-domain" type="checkbox" value="{{ $key }}" id="sd_{{ $key }}">
                                                            <label class="form-check-label" for="sd_{{ $key }}">{{ __($label) }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Preferred state')</label>
                                            <input type="text" id="suStState" class="form-control form--control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Preferred city')</label>
                                            <input type="text" id="suStCity" class="form-control form--control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form--label">@lang('Referral code (optional)')</label>
                                            <input type="text" id="suStReferral" class="form-control form--control" maxlength="32">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn--base w-100 mt-4" id="suStContinueOtp">@lang('Continue with OTP')</button>
                                </div>

                                <div id="stepOtp" class="d-none mt-4 pt-4 border-top">
                                    <h6 class="mb-3 text--base">@lang('Verify contact')</h6>
                                    <p class="small text-muted" id="suStOtpHint"></p>
                                    <div class="form-group mb-3">
                                        <label class="form--label">@lang('Verification code')</label>
                                        <input type="text" id="suStOtp" class="form-control form--control" maxlength="8" autocomplete="one-time-code">
                                    </div>
                                    <button type="button" class="btn btn--base w-100" id="suStVerify">@lang('Verify & continue')</button>
                                </div>

                                <p class="text-danger small mt-3 d-none" id="suStErr"></p>
                            </div>

                            <p class="small text-muted mt-4 mb-0">
                                @lang('Already registered?')
                                <button type="button" class="btn btn-link btn-sm p-0 align-baseline" data-bs-toggle="modal" data-bs-target="#otpAuthModal">@lang('Login')</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('Template::partials.otp_signup_modal')
@endsection

@push('script')
    <script>
        (function() {
            const root = document.getElementById('signupStudentRoot');
            if (!root) return;
            const csrf = root.dataset.csrf;

            function fd(obj) {
                const f = new FormData();
                Object.keys(obj).forEach(k => {
                    if (Array.isArray(obj[k])) obj[k].forEach(v => f.append(k + '[]', v));
                    else f.append(k, obj[k] ?? '');
                });
                f.append('_token', csrf);
                return f;
            }

            async function post(url, formData) {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await res.json();
                return {
                    res,
                    data
                };
            }

            function selectedDomains() {
                return Array.from(root.querySelectorAll('.su-st-domain:checked')).map(x => x.value);
            }

            root.querySelector('#suStContinueOtp').addEventListener('click', async function() {
                const err = root.querySelector('#suStErr');
                err.classList.add('d-none');
                const domains = selectedDomains();
                if (!domains.length) {
                    err.textContent = @json(__('Select at least one domain.'));
                    err.classList.remove('d-none');
                    return;
                }
                const body = {
                    firstname: root.querySelector('#suStFirstname').value.trim(),
                    lastname: root.querySelector('#suStLastname').value.trim(),
                    contact: root.querySelector('#suStContact').value.trim(),
                    qualification: root.querySelector('#suStQual').value,
                    preferred_domains: domains,
                    preferred_state: root.querySelector('#suStState').value.trim(),
                    preferred_city: root.querySelector('#suStCity').value.trim(),
                    referral_code: root.querySelector('#suStReferral').value.trim(),
                };
                const startR = await post(root.dataset.startUrl, fd(body));
                if (!startR.res.ok || !startR.data.ok) {
                    err.textContent = startR.data.message || @json(__('Check the form and try again.'));
                    if (startR.data.errors) {
                        const first = Object.values(startR.data.errors)[0];
                        if (first && first[0]) err.textContent = first[0];
                    }
                    err.classList.remove('d-none');
                    return;
                }
                const sentR = await post(root.dataset.sendUrl, fd({
                    contact: body.contact,
                    guard_target: 'web'
                }));
                if (!sentR.res.ok || !sentR.data.ok) {
                    err.textContent = sentR.data.message || @json(__('Could not send OTP.'));
                    err.classList.remove('d-none');
                    return;
                }
                root.querySelector('#stepOtp').classList.remove('d-none');
                root.querySelector('#suStOtpHint').textContent = sentR.data.message || '';
                root.querySelector('#stepDetails').querySelectorAll('input,select,button').forEach(el => {
                    if (el.id !== 'suStContinueOtp') el.disabled = true;
                });
                root.querySelector('#suStContinueOtp').classList.add('d-none');
            });

            root.querySelector('#suStVerify').addEventListener('click', async function() {
                const err = root.querySelector('#suStErr');
                err.classList.add('d-none');
                const contact = root.querySelector('#suStContact').value.trim();
                const otp = root.querySelector('#suStOtp').value.trim();
                const vr = await post(root.dataset.verifyUrl, fd({
                    contact,
                    otp,
                    guard_target: 'web'
                }));
                const data = vr.data;
                if (vr.res.ok && data.ok && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                err.textContent = data.message || @json(__('Invalid code.'));
                err.classList.remove('d-none');
            });
        })();
    </script>
@endpush
