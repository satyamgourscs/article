@extends($activeTemplate . 'layouts.app')
@section('panel')
    <section class="account py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="mb-2">@lang('Company signup')</h4>
                            <p class="text-muted small mb-4">@lang('Register your firm with email or mobile and a one-time code.')</p>
                            @if (config('otp.test_mode'))
                                <div class="alert alert-warning small mb-3">{{ config('otp.test_banner') }}</div>
                            @endif

                            <div id="signupCoRoot" data-start-url="{{ route('signup.company.start') }}"
                                data-send-url="{{ route('auth.otp.send') }}" data-verify-url="{{ route('auth.otp.verify') }}"
                                data-csrf="{{ csrf_token() }}">

                                <div id="coStepDetails">
                                    <h6 class="mb-3 text--base">@lang('Firm details')</h6>
                                    <div class="row gy-3">
                                        <div class="col-12">
                                            <label class="form--label">@lang('Firm name') <span class="text--danger">*</span></label>
                                            <input type="text" id="suCoFirm" class="form-control form--control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Contact person — first name') <span class="text--danger">*</span></label>
                                            <input type="text" id="suCoFirst" class="form-control form--control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Contact person — last name') <span class="text--danger">*</span></label>
                                            <input type="text" id="suCoLast" class="form-control form--control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form--label">@lang('Email or mobile') <span class="text--danger">*</span></label>
                                            <input type="text" id="suCoContact" class="form-control form--control" autocomplete="username" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Firm type') <span class="text--danger">*</span></label>
                                            <select id="suCoType" class="form-select form--control" required>
                                                <option value="">@lang('Select')</option>
                                                @foreach ($firmTypes as $key => $label)
                                                    <option value="{{ $key }}">{{ __($label) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('State')</label>
                                            <input type="text" id="suCoState" class="form-control form--control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('City')</label>
                                            <input type="text" id="suCoCity" class="form-control form--control">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn--base w-100 mt-4" id="suCoContinueOtp">@lang('Continue with OTP')</button>
                                </div>

                                <div id="coStepOtp" class="d-none mt-4 pt-4 border-top">
                                    <h6 class="mb-3 text--base">@lang('Verify contact')</h6>
                                    <p class="small text-muted" id="suCoOtpHint"></p>
                                    <div class="form-group mb-3">
                                        <label class="form--label">@lang('Verification code')</label>
                                        <input type="text" id="suCoOtp" class="form-control form--control" maxlength="8" autocomplete="one-time-code">
                                    </div>
                                    <button type="button" class="btn btn--base w-100" id="suCoVerify">@lang('Verify & continue')</button>
                                </div>

                                <p class="text-danger small mt-3 d-none" id="suCoErr"></p>
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
            const root = document.getElementById('signupCoRoot');
            if (!root) return;
            const csrf = root.dataset.csrf;

            function fd(obj) {
                const f = new FormData();
                Object.keys(obj).forEach(k => f.append(k, obj[k] ?? ''));
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

            root.querySelector('#suCoContinueOtp').addEventListener('click', async function() {
                const err = root.querySelector('#suCoErr');
                err.classList.add('d-none');
                const body = {
                    firm_name: root.querySelector('#suCoFirm').value.trim(),
                    firstname: root.querySelector('#suCoFirst').value.trim(),
                    lastname: root.querySelector('#suCoLast').value.trim(),
                    contact: root.querySelector('#suCoContact').value.trim(),
                    firm_type: root.querySelector('#suCoType').value,
                    state: root.querySelector('#suCoState').value.trim(),
                    city: root.querySelector('#suCoCity').value.trim(),
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
                    guard_target: 'buyer'
                }));
                if (!sentR.res.ok || !sentR.data.ok) {
                    err.textContent = sentR.data.message || @json(__('Could not send OTP.'));
                    err.classList.remove('d-none');
                    return;
                }
                root.querySelector('#coStepOtp').classList.remove('d-none');
                root.querySelector('#suCoOtpHint').textContent = sentR.data.message || '';
                root.querySelector('#coStepDetails').querySelectorAll('input,select,button').forEach(el => {
                    if (el.id !== 'suCoContinueOtp') el.disabled = true;
                });
                root.querySelector('#suCoContinueOtp').classList.add('d-none');
            });

            root.querySelector('#suCoVerify').addEventListener('click', async function() {
                const err = root.querySelector('#suCoErr');
                err.classList.add('d-none');
                const contact = root.querySelector('#suCoContact').value.trim();
                const otp = root.querySelector('#suCoOtp').value.trim();
                const vr = await post(root.dataset.verifyUrl, fd({
                    contact,
                    otp,
                    guard_target: 'buyer'
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
