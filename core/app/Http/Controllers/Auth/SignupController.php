<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpAuthService;
use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function student()
    {
        $pageTitle = __('Student signup');
        $qualifications = config('student_profile.qualifications', []);
        $domains = config('student_profile.domains', []);

        return view('Template::auth.signup.student', compact('pageTitle', 'qualifications', 'domains'));
    }

    public function startStudent(Request $request, OtpAuthService $otp)
    {
        $domainKeys = implode(',', array_keys(config('student_profile.domains', [])));
        $qualKeys = implode(',', array_keys(config('student_profile.qualifications', [])));

        $validated = $request->validate([
            'firstname' => 'required|string|max:80',
            'lastname' => 'required|string|max:80',
            'contact' => 'required|string|max:191',
            'qualification' => 'required|string|in:'.$qualKeys,
            'preferred_domains' => 'required|array|min:1',
            'preferred_domains.*' => 'in:'.$domainKeys,
            'preferred_state' => 'nullable|string|max:191',
            'preferred_city' => 'nullable|string|max:191',
            'referral_code' => 'nullable|string|max:32',
        ]);

        $norm = $otp->normalizeContact($validated['contact']);
        $validated['_contact_key'] = $norm['key'];
        $request->session()->put('otp_signup_student', $validated);

        return response()->json([
            'ok' => true,
            'contact' => $validated['contact'],
            'guard_target' => 'web',
        ]);
    }

    public function company()
    {
        $pageTitle = __('Company signup');
        $firmTypes = config('signup.firm_types', []);

        return view('Template::auth.signup.company', compact('pageTitle', 'firmTypes'));
    }

    public function startCompany(Request $request, OtpAuthService $otp)
    {
        $firmTypeKeys = implode(',', array_keys(config('signup.firm_types', [])));

        $validated = $request->validate([
            'firm_name' => 'required|string|max:191',
            'firstname' => 'required|string|max:80',
            'lastname' => 'required|string|max:80',
            'contact' => 'required|string|max:191',
            'firm_type' => 'required|string|in:'.$firmTypeKeys,
            'state' => 'nullable|string|max:191',
            'city' => 'nullable|string|max:191',
        ]);

        $norm = $otp->normalizeContact($validated['contact']);
        $validated['_contact_key'] = $norm['key'];
        $request->session()->put('otp_signup_company', $validated);

        return response()->json([
            'ok' => true,
            'contact' => $validated['contact'],
            'guard_target' => 'buyer',
        ]);
    }
}
