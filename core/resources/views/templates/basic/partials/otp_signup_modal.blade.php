@php
    $otpSend = route('auth.otp.send');
    $otpVerify = route('auth.otp.verify');
    $token = csrf_token();
@endphp

<div class="modal fade" id="otpAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="otpAuthModalTitle">@lang('Login')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div id="otpStepLoginChoose" class="otp-step">
                    <p class="text-muted small mb-3">@lang('CA Student / Firm login')</p>
                    <button type="button" class="btn btn--base w-100 mb-2" data-guard="web">@lang('Student')</button>
                    <button type="button" class="btn btn-outline--base w-100 mb-2" data-guard="buyer">@lang('Firm')</button>
                    @if (! auth()->guard('web')->check() && ! auth()->guard('buyer')->check())
                        <p class="text-muted small mb-0 mt-3">@lang('New here?')</p>
                        <a href="{{ route('signup.student') }}" class="btn btn-link w-100 p-0 text-start">@lang('CA Student signup')</a>
                        <a href="{{ route('signup.company') }}" class="btn btn-link w-100 p-0 text-start">@lang('CA Firm signup')</a>
                    @endif
                </div>
                <div id="otpStepContact" class="otp-step d-none">
                    @if (config('otp.test_mode'))
                        <div class="alert alert-warning small mb-3" role="alert">{{ config('otp.test_banner') }}</div>
                    @endif
                    <div class="form-group mb-2">
                        <label class="form--label">@lang('Email or mobile')</label>
                        <input type="text" class="form-control form--control" id="modalOtpContact" autocomplete="username">
                    </div>
                    <button type="button" class="btn btn-outline--base w-100 mb-3" id="modalOtpSend">@lang('Send OTP')</button>
                    <div class="form-group mb-2">
                        <label class="form--label">@lang('Verification code')</label>
                        <input type="text" class="form-control form--control" id="modalOtpCode" maxlength="8"
                            autocomplete="one-time-code">
                    </div>
                    <button type="button" class="btn btn--base w-100" id="modalOtpVerifyBtn">@lang('Verify')</button>
                    <p class="text-muted small mt-2 d-none" id="modalOtpMsg"></p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function() {
            const modalEl = document.getElementById('otpAuthModal');
            if (!modalEl || typeof bootstrap === 'undefined') return;
            const modal = new bootstrap.Modal(modalEl);
            let guardTarget = 'web';

            function showStep(name) {
                modalEl.querySelectorAll('.otp-step').forEach(el => el.classList.add('d-none'));
                modalEl.querySelector('#otpStep' + name).classList.remove('d-none');
            }

            async function postJson(url, body) {
                const fd = new FormData();
                Object.keys(body).forEach(k => fd.append(k, body[k] ?? ''));
                fd.append('_token', @json($token));
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                const data = await res.json();
                return {
                    res,
                    data
                };
            }

            document.querySelectorAll('[data-bs-target="#otpAuthModal"], [data-open-otp-modal]').forEach(el => {
                el.addEventListener('click', function() {
                    document.getElementById('otpAuthModalTitle').textContent = @json(__('Login'));
                    showStep('LoginChoose');
                    modal.show();
                });
            });

            modalEl.querySelectorAll('#otpStepLoginChoose [data-guard]').forEach(btn => {
                btn.addEventListener('click', function() {
                    guardTarget = this.getAttribute('data-guard');
                    showStep('Contact');
                });
            });

            modalEl.querySelector('#modalOtpSend').addEventListener('click', async function() {
                const contact = modalEl.querySelector('#modalOtpContact').value.trim();
                const msg = modalEl.querySelector('#modalOtpMsg');
                if (!contact) {
                    alert(@json(__('Enter your email or mobile number.')));
                    return;
                }
                const {
                    res,
                    data
                } = await postJson(@json($otpSend), {
                    contact,
                    guard_target: guardTarget
                });
                if (res.ok && data.ok) {
                    msg.classList.remove('d-none');
                    msg.textContent = data.message || '';
                } else {
                    alert(data.message || @json(__('Unable to send code.')));
                }
            });

            modalEl.querySelector('#modalOtpVerifyBtn').addEventListener('click', async function() {
                const contact = modalEl.querySelector('#modalOtpContact').value.trim();
                const otp = modalEl.querySelector('#modalOtpCode').value.trim();
                if (!contact || !otp) {
                    alert(@json(__('Enter contact and code.')));
                    return;
                }

                const {
                    res,
                    data
                } = await postJson(@json($otpVerify), {
                    contact,
                    otp,
                    guard_target: guardTarget
                });

                if (res.ok && data.ok && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                alert(data.message || @json(__('Invalid code.')));
            });

            modalEl.addEventListener('hidden.bs.modal', function() {
                modalEl.querySelector('#modalOtpContact').value = '';
                modalEl.querySelector('#modalOtpCode').value = '';
                modalEl.querySelector('#modalOtpMsg').classList.add('d-none');
                showStep('LoginChoose');
            });
        })();
    </script>
@endpush
