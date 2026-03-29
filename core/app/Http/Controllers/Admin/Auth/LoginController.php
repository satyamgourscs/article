<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laramin\Utility\Onumoti;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public $redirectTo = 'admin';

    public function showLoginForm()
    {
        $pageTitle = 'Admin Login';

        return view('admin.auth.login', compact('pageTitle'));
    }

    protected function guard()
    {
        return auth()->guard('admin');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $request->session()->regenerateToken();

        if (! verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];

            return back()->withNotify($notify);
        }

        Onumoti::getData();

        if (method_exists($this, 'hasTooManyLoginAttempts')
            && $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard('admin')->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }
}
