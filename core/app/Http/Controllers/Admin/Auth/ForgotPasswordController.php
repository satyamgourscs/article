<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminPasswordReset;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        $pageTitle = __('Recover Account');

        return view('admin.auth.passwords.email', compact('pageTitle'));
    }

    public function sendResetCodeEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        if (! verifyCaptcha()) {
            $notify[] = ['error', __('Invalid captcha provided')];

            return back()->withNotify($notify)->withInput();
        }

        $admin = Admin::where('email', $request->email)->first();
        if (! $admin) {
            $notify[] = ['success', __('If this email is registered, you will receive a verification code shortly.')];

            return back()->withNotify($notify)->withInput();
        }

        AdminPasswordReset::where('email', $admin->email)->delete();

        $code = (string) random_int(100000, 999999);
        AdminPasswordReset::create([
            'email' => $admin->email,
            'token' => $code,
            'status' => Status::ENABLE,
            'created_at' => Carbon::now(),
        ]);

        $ipInfo = getIpInfo();
        $browser = osBrowser();
        notify($admin, 'PASS_RESET_CODE', [
            'code' => $code,
            'ip' => $ipInfo['ip'],
            'browser' => $browser['browser'],
            'operating_system' => $browser['os_platform'],
            'time' => $ipInfo['time'],
        ], ['email'], false);

        $request->session()->put('fpass_email', $admin->email);

        $notify[] = ['success', __('Verification code sent to your email.')];

        return to_route('admin.password.code.verify')->withNotify($notify);
    }

    public function codeVerify()
    {
        if (! session('fpass_email')) {
            $notify[] = ['error', __('Session expired. Request a new code.')];

            return to_route('admin.password.reset')->withNotify($notify);
        }

        $pageTitle = __('Verify Code');

        return view('admin.auth.passwords.code_verify', compact('pageTitle'));
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $email = session('fpass_email');
        if (! $email) {
            $notify[] = ['error', __('Session expired. Request a new code.')];

            return to_route('admin.password.reset')->withNotify($notify);
        }

        $reset = AdminPasswordReset::where('email', $email)
            ->where('token', $request->code)
            ->where('status', Status::ENABLE)
            ->first();

        if (! $reset) {
            $notify[] = ['error', __('Invalid verification code')];

            return back()->withNotify($notify);
        }

        return redirect()->route('admin.password.reset.form', $reset->token);
    }
}
