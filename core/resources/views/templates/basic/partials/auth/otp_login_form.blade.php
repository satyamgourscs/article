@php
    $otpSendUrl = route('auth.otp.send');
    $otpVerifyUrl = route('auth.otp.verify');
    $otpGuard = $otpGuardTarget ?? 'web';
@endphp

<div class="otp-login-flow" data-guard-target="{{ $otpGuard }}" data-send-url="{{ $otpSendUrl }}"
    data-verify-url="{{ $otpVerifyUrl }}" data-csrf="{{ csrf_token() }}">
    @if (config('otp.test_mode'))
        <div class="alert alert-warning small mb-3" role="alert">{{ config('otp.test_banner') }}</div>
    @endif
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <label class="form--label">@lang('Email or mobile')</label>
                <input type="text" class="form-control form--control" name="contact" id="otpContact" required
                    autocomplete="username" value="{{ old('contact') }}">
            </div>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-outline--base w-100 mb-3" id="otpSendBtn">@lang('Send OTP')</button>
        </div>
        <div class="col-12">
            <div class="form-group">
                <label class="form--label">@lang('Verification code')</label>
                <input type="text" name="otp" class="form-control form--control" id="otpCode" required
                    maxlength="8" autocomplete="one-time-code" placeholder="6-digit code">
            </div>
        </div>
        <x-captcha />
        <div class="col-12 form-group">
            <button type="submit" class="btn btn--base w-100" id="otpVerifySubmit">@lang('Verify')</button>
        </div>
    </div>
    <p class="text-muted small mt-2 d-none" id="otpStatusMsg"></p>
</div>

@push('script')
    <script>
        (function() {
            const root = document.querySelector('.otp-login-flow');
            if (!root) return;
            const csrf = root.dataset.csrf;
            const sendUrl = root.dataset.sendUrl;
            const verifyUrl = root.dataset.verifyUrl;
            const guardTarget = root.dataset.guardTarget;

            async function postJson(url, body) {
                const fd = new FormData();
                Object.keys(body).forEach(k => fd.append(k, body[k]));
                fd.append('_token', csrf);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: fd
                });
                return res.json();
            }

            const sendBtn = root.querySelector('#otpSendBtn');
            const msg = root.querySelector('#otpStatusMsg');
            sendBtn.addEventListener('click', async function() {
                const contact = root.querySelector('#otpContact').value.trim();
                if (!contact) {
                    alert(@json(__('Enter your email or mobile number.')));
                    return;
                }
                sendBtn.disabled = true;
                const data = await postJson(sendUrl, {
                    contact,
                    guard_target: guardTarget
                });
                sendBtn.disabled = false;
                if (data.ok) {
                    msg.classList.remove('d-none');
                    msg.textContent = data.message || '';
                } else {
                    alert(data.message || @json(__('Unable to send code.')));
                }
            });
        })();
    </script>
@endpush
