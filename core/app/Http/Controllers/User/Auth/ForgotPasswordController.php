<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $notify[] = ['info', __('Password recovery is disabled. Sign in with a one-time code sent to your email or mobile.')];

        return to_route('user.login')->withNotify($notify);
    }

    public function sendResetCodeEmail(Request $request)
    {
        $notify[] = ['info', __('Password recovery is disabled. Use OTP login instead.')];

        return to_route('user.login')->withNotify($notify);
    }

    public function findFieldType()
    {
        $input = request()->input('value');

        $fieldType = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $input]);
        return $fieldType;
    }

    public function codeVerify(Request $request)
    {
        return to_route('user.login')->withNotify(['info' => __('Password recovery is disabled. Use OTP login instead.')]);
    }

    public function verifyCode(Request $request)
    {
        return to_route('user.login')->withNotify(['info' => __('Password recovery is disabled. Use OTP login instead.')]);
    }

}
