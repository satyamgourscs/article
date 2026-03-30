<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Support\SafeSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Rules\FileTypeValidate;

class ProfileController extends Controller
{
    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = auth()->guard('buyer')->user();
        return view('Template::buyer.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $imageRule = 'nullable';
        $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:191',
            'state' => 'nullable|string|max:191',
            'zip' => 'nullable|string|max:32',
            'pincode' => 'nullable|string|max:32',
            'skills' => 'nullable|array|max:50',
            'skills.*' => 'nullable|string|max:100',
            'language'   => 'required|array|min:1|max:10',
            'language.*' => 'nullable|string',
            'image'       => ["$imageRule", new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ],[
            'firstname.required'=>'The first name field is required',
            'lastname.required'=>'The last name field is required'
        ]);

        $user = auth()->guard('buyer')->user();

        if ($request->hasFile('image')) {
            try {
                $user->image = fileUploader($request->image, getFilePath('buyerProfile'), getFileSize('buyerProfile'), @$user->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;

        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        if (SafeSchema::hasColumn('buyers', 'pincode')) {
            $user->pincode = $request->input('pincode', $request->zip);
        }
        $user->country_name = 'India';
        $user->country_code = 'IN';
        if (SafeSchema::hasColumn('buyers', 'country')) {
            $user->country = 'India';
        }
        if (SafeSchema::hasColumn('buyers', 'preferred_state')) {
            $user->preferred_state = $request->state;
        }
        if (SafeSchema::hasColumn('buyers', 'preferred_city')) {
            $user->preferred_city = $request->city;
        }
        if (SafeSchema::hasColumn('buyers', 'skills') && $request->filled('skills')) {
            $skillsList = array_values(array_filter(array_map(
                static fn ($s) => is_string($s) ? trim($s) : '',
                $request->input('skills', [])
            )));
            $user->skills = $skillsList;
        }

        $user->language = $request->language;

        $user->save();

        if (SafeSchema::hasTable('company_profiles') && $user->companyProfile) {
            $payload = [
                'state' => $request->state,
                'city' => $request->city,
            ];
            if (SafeSchema::hasColumn('company_profiles', 'pincode')) {
                $payload['pincode'] = $request->input('pincode', $request->zip);
            }
            if (SafeSchema::hasColumn('company_profiles', 'country')) {
                $payload['country'] = 'India';
            }
            $user->companyProfile->update($payload);
        }
        $notify[] = ['success', 'Profile updated successfully'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';
        return view('Template::buyer.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required','confirmed',$passwordValidation]
        ]);

        $user = auth()->guard('buyer')->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'The password doesn\'t match!'];
            return back()->withNotify($notify);
        }
    }
}
