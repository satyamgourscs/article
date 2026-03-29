<?php

namespace App\Http\Controllers\Buyer\Auth;

use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Constants\Status;
use App\Models\UserLogin;
use App\Services\OtpAuthService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;


    protected $username;


    public function __construct()
    {
        parent::__construct();
        $this->username = $this->findUsername();
    }

    protected function guard()
    {
        return auth()->guard('buyer');
    }

    public function showLoginForm()
    {
        $pageTitle = "Login";
        Intended::identifyRoute();
        return view('Template::buyer.auth.login', compact('pageTitle'));
    }

    public function login(Request $request)
    {
        if ($request->filled('password')) {
            Intended::reAssignSession();
            $notify[] = ['error', __('Password login is disabled. Use the verification code sent to your email or mobile.')];

            return back()->withNotify($notify);
        }

        $request->validate([
            'contact' => 'required|string|max:191',
            'otp' => 'required|string|max:8',
        ]);

        if (! verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];

            return back()->withNotify($notify);
        }

        try {
            $result = app(OtpAuthService::class)->verifyOtp($request->contact, $request->otp, 'buyer');
        } catch (\Throwable $e) {
            Intended::reAssignSession();
            $notify[] = ['error', $e->getMessage()];

            return back()->withNotify($notify);
        }

        if ($result['action'] === 'login' && isset($result['buyer'])) {
            $this->guard()->login($result['buyer']);

            return $this->sendLoginResponse($request);
        }

        if ($result['action'] === 'register') {
            $otpService = app(OtpAuthService::class);
            $signupProfile = $otpService->signupProfileForContactFromSession(
                $request->session(),
                $result['guard_target'],
                $result['norm']['key']
            );

            if ($signupProfile === null) {
                Intended::reAssignSession();
                $notify[] = ['error', __('Create your firm account from the company signup page, then sign in with your code here.')];

                return back()->withNotify($notify);
            }

            try {
                $buyer = $otpService->createAccountFromOtpVerify($result['norm'], 'buyer', $signupProfile);
            } catch (\Throwable $e) {
                Intended::reAssignSession();
                $notify[] = ['error', $e->getMessage()];

                return back()->withNotify($notify);
            }

            $request->session()->forget(['otp_registration', 'otp_signup_student', 'otp_signup_company']);
            $this->guard()->login($buyer);
            $request->session()->regenerate();

            return $this->sendLoginResponse($request);
        }

        Intended::reAssignSession();
        $notify[] = ['error', __('Unable to sign you in.')];

        return back()->withNotify($notify);
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin($request)
    {
        $validator = Validator::make($request->all(), [
            'contact' => 'required|string',
            'otp' => 'required|string',
        ]);
        if ($validator->fails()) {
            Intended::reAssignSession();
            $validator->validate();
        }
    }

    public function logout()
    {
        $this->guard()->logout();
        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return to_route('buyer.login')->withNotify($notify);
    }


    public function authenticated(Request $request, $user)
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->buyer_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();

        $redirection = Intended::getRedirection();
        return $redirection ? $redirection : to_route('buyer.home');
    }
}
