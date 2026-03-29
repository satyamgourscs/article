<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Concerns\RecordsOtpAuthenticatedLogin;
use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\User;
use App\Services\OtpAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpAuthController extends Controller
{
    use RecordsOtpAuthenticatedLogin;

    public function completeForm(Request $request)
    {
        $registration = $request->session()->get('otp_registration');
        $guard = is_array($registration) ? ($registration['guard_target'] ?? null) : null;

        $notify[] = ['info', __('Sign up is completed in one step. Use the student or company signup page, then verify your code.')];
        $request->session()->forget('otp_registration');

        if ($guard === 'buyer') {
            return to_route('signup.company')->withNotify($notify);
        }

        return to_route('signup.student')->withNotify($notify);
    }

    public function send(Request $request, OtpAuthService $otp): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string|max:191',
            'guard_target' => 'required|in:web,buyer',
        ]);

        try {
            $otp->sendOtp($data['contact'], $data['guard_target']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['ok' => true, 'message' => __('Verification code sent.')]);
    }

    public function verify(Request $request, OtpAuthService $otp): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'contact' => 'required|string|max:191',
            'otp' => 'required|string|max:8',
            'guard_target' => 'required|in:web,buyer',
        ]);

        try {
            $result = $otp->verifyOtp($data['contact'], $data['otp'], $data['guard_target']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        if ($result['action'] === 'login') {
            if (isset($result['user'])) {
                Auth::guard('web')->login($result['user']);
                $this->recordUserLogin($request, $result['user']);
                $request->session()->regenerate();

                return response()->json(['ok' => true, 'redirect' => route('user.home')]);
            }
            if (isset($result['buyer'])) {
                Auth::guard('buyer')->login($result['buyer']);
                $this->recordBuyerLogin($request, $result['buyer']);
                $request->session()->regenerate();

                return response()->json(['ok' => true, 'redirect' => route('buyer.home')]);
            }
        }

        $guardTarget = (string) ($result['guard_target'] ?? '');
        $contactKey = (string) ($result['norm']['key'] ?? '');
        $signupProfile = $otp->signupProfileForContactFromSession($request->session(), $guardTarget, $contactKey);

        if ($signupProfile === null) {
            return response()->json([
                'ok' => false,
                'message' => __('Your signup session expired or does not match this contact. Please start again from the signup page.'),
            ], 422);
        }

        try {
            $account = $otp->createAccountFromOtpVerify($result['norm'], $guardTarget, $signupProfile);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        if ($account instanceof User) {
            Auth::guard('web')->login($account);
            $this->recordUserLogin($request, $account);
            $redirect = route('user.home');
        } elseif ($account instanceof Buyer) {
            Auth::guard('buyer')->login($account);
            $this->recordBuyerLogin($request, $account);
            $redirect = route('buyer.home');
        } else {
            return response()->json(['ok' => false, 'message' => __('Invalid registration channel.')], 422);
        }

        $request->session()->forget(['otp_registration', 'otp_signup_student', 'otp_signup_company']);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'redirect' => $redirect]);
    }

    public function complete(Request $request, OtpAuthService $otp): \Illuminate\Http\JsonResponse
    {
        $session = $request->session()->get('otp_registration');
        if (! $session || ($session['expires_at'] ?? 0) < now()->timestamp) {
            return response()->json(['ok' => false, 'message' => __('Session expired. Verify your code again.')], 422);
        }

        $data = $request->validate([
            'firstname' => 'required|string|max:80',
            'lastname' => 'required|string|max:80',
            'referral_code' => 'nullable|string|max:32',
        ]);

        $profile = is_array($session['signup_profile'] ?? null) ? $session['signup_profile'] : [];
        $referralInput = $data['referral_code'] ?? null;
        if ($referralInput === null || $referralInput === '') {
            $referralInput = $profile['referral_code'] ?? null;
        }

        $merged = array_merge(
            $profile,
            [
                'firstname' => $data['firstname'],
                'lastname' => $data['lastname'],
                'referral_code' => $referralInput,
            ]
        );

        $norm = $otp->normFromContactKey($session['contact']);
        $guard = $session['guard_target'];

        try {
            $account = $otp->createAccountFromOtpVerify($norm, $guard, $merged);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        if ($account instanceof User) {
            Auth::guard('web')->login($account);
            $this->recordUserLogin($request, $account);
            $redirect = route('user.home');
        } elseif ($account instanceof Buyer) {
            Auth::guard('buyer')->login($account);
            $this->recordBuyerLogin($request, $account);
            $redirect = route('buyer.home');
        } else {
            return response()->json(['ok' => false, 'message' => __('Invalid registration channel.')], 422);
        }

        $request->session()->forget(['otp_registration', 'otp_signup_student', 'otp_signup_company']);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'redirect' => $redirect]);
    }
}
