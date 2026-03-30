<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\GoogleAuthenticator;
use App\Models\Education;
use App\Models\Portfolio;
use App\Models\Skill;
use App\Models\Transaction;
use App\Models\User;
use App\Rules\FileTypeValidate;
use App\Support\SafeSchema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->guard('buyer')->check()) {
                $notify[] = ['error', __('This profile wizard is for student accounts only.')];

                return to_route('buyer.home')->withNotify($notify);
            }

            return $next($request);
        });
    }

    public function changePassword()
    {
        $pageTitle = 'Change Password';

        return view('Template::user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {
        $passwordValidation = Password::min(6);
        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $passwordValidation],
        ]);

        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
            $notify[] = ['success', 'Password changed successfully'];

            return back()->withNotify($notify);
        }
        $notify[] = ['error', 'The password doesn\'t match!'];

        return back()->withNotify($notify);
    }

    /**
     * Step 2 — skills, experience, expertise (no duplicate personal fields).
     */
    public function skill()
    {
        if (! SafeSchema::hasTable('student_profiles')) {
            $notify[] = ['warning', __('Student profile is not available yet. Run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $user = auth()->user()->load('studentProfile');
        if (! $user->studentProfile) {
            $notify[] = ['info', __('Complete basic information first.')];

            return to_route('user.profile.setting')->withNotify($notify);
        }

        $pageTitle = __('Skills & experience');
        $skills = Skill::active()->orderBy('name')->get();
        $studentProfile = $user->studentProfile;
        $expertiseLevels = config('student_profile.expertise_levels', []);

        $selectedSkills = old('skills');
        if (! is_array($selectedSkills)) {
            $selectedSkills = [];
            if (SafeSchema::hasColumn('student_profiles', 'skills')) {
                $raw = $studentProfile->skills ?? null;
                $selectedSkills = is_array($raw) ? $raw : [];
            }
        }
        if (count($selectedSkills) === 0) {
            $ids = is_array($user->skill_ids) ? array_filter($user->skill_ids) : [];
            if (count($ids) > 0) {
                $selectedSkills = Skill::whereIn('id', $ids)->pluck('name')->all();
            }
        }

        $selectedSkillKeys = array_flip(array_map('mb_strtolower', $selectedSkills));

        return view('Template::user.profile.skill', compact(
            'pageTitle',
            'user',
            'skills',
            'studentProfile',
            'expertiseLevels',
            'selectedSkills',
            'selectedSkillKeys'
        ));
    }

    public function submitSkills(Request $request)
    {
        if (! SafeSchema::hasTable('student_profiles')) {
            $notify[] = ['warning', __('Student profile is not available yet. Run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $user = auth()->user();
        if (! $user->studentProfile) {
            $notify[] = ['error', __('Save basic information before skills.')];

            return to_route('user.profile.setting')->withNotify($notify);
        }

        $levelKeysArr = array_keys(config('student_profile.expertise_levels', []));
        $expertiseRule = count($levelKeysArr) ? 'nullable|string|in:'.implode(',', $levelKeysArr) : 'nullable|string|max:64';

        $request->validate([
            'skills' => 'required|array|min:1',
            'skills.*' => 'required|string|max:100',
            'training_experience' => 'nullable|string',
            'experience_years' => 'nullable|string|max:32',
            'expertise_level' => $expertiseRule,
        ]);

        $seen = [];
        $skillsList = [];
        foreach ($request->skills as $raw) {
            $t = trim((string) $raw);
            if ($t === '') {
                continue;
            }
            $k = mb_strtolower($t);
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $skillsList[] = $t;
        }
        if (count($skillsList) < 1) {
            return back()->withErrors(['skills' => __('Select or add at least one skill.')])->withInput();
        }

        $skillIdsMatched = [];
        foreach ($skillsList as $tag) {
            $sk = Skill::query()->active()->whereRaw('LOWER(name) = ?', [mb_strtolower($tag)])->first();
            if ($sk) {
                $skillIdsMatched[] = (int) $sk->id;
            }
        }
        $skillIdsMatched = array_values(array_unique($skillIdsMatched));
        $user->skills()->sync($skillIdsMatched);
        $user->skill_ids = $skillIdsMatched;
        if ($user->step < 2) {
            $user->step = 2;
        }
        if (SafeSchema::hasColumn('users', 'skills')) {
            $user->skills = $skillsList;
        }
        $user->save();

        $spData = [
            'training_experience' => $request->training_experience,
        ];
        if (SafeSchema::hasColumn('student_profiles', 'skills')) {
            $spData['skills'] = $skillsList;
        }
        if (SafeSchema::hasColumn('student_profiles', 'experience_years')) {
            $spData['experience_years'] = $request->experience_years;
        }
        if (SafeSchema::hasColumn('student_profiles', 'expertise_level')) {
            $spData['expertise_level'] = $request->expertise_level;
        }
        $user->studentProfile->update($spData);

        $notify[] = ['success', __('Skills saved. Add work samples or publish when ready.')];

        return to_route('user.profile.portfolio')->withNotify($notify);
    }

    /**
     * Step 1 — basic info, location, category, resume (single place for bio).
     */
    public function profile()
    {
        if (! SafeSchema::hasTable('student_profiles')) {
            $notify[] = ['warning', __('Student profile is not available yet. Run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = __('Basic information');
        $user = auth()->user()->load('studentProfile');
        $qualifications = config('student_profile.qualifications', []);
        $educationStatuses = config('student_profile.education_statuses', []);
        $domains = config('student_profile.domains', []);

        return view('Template::user.profile.setting', compact(
            'pageTitle',
            'user',
            'qualifications',
            'educationStatuses',
            'domains'
        ));
    }

    public function submitProfile(Request $request)
    {
        if (! SafeSchema::hasTable('student_profiles')) {
            $notify[] = ['warning', __('Student profile is not available yet. Run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $domainKeys = implode(',', array_keys(config('student_profile.domains', [])));
        $qualKeys = implode(',', array_keys(config('student_profile.qualifications', [])));
        $statusKeys = implode(',', array_keys(config('student_profile.education_statuses', [])));

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')), true) ?: [];
        $countryNames = array_values(array_unique(array_column($countryData, 'country')));

        $imageRule = 'nullable';
        $request->validate([
            'firstname' => 'required|string|max:80',
            'lastname' => 'required|string|max:80',
            'mobile' => 'required|string|max:40',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:120',
            'zip' => 'nullable|string|max:32',
            'country' => ['required', 'string', Rule::in($countryNames)],
            'country_code' => ['required', 'string', Rule::in(array_keys($countryData))],
            'short_bio' => 'nullable|string|max:5000',
            'qualification' => 'required|string|in:'.$qualKeys,
            'education_status' => 'required|string|in:'.$statusKeys,
            'preferred_domains' => 'nullable|array',
            'preferred_domains.*' => 'in:'.$domainKeys,
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'image' => [$imageRule, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $user = auth()->user();

        if ($request->hasFile('image')) {
            try {
                $user->image = fileUploader($request->image, getFilePath('userProfile'), getFileSize('userProfile'), @$user->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload user image'];

                return back()->withNotify($notify);
            }
        }

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->mobile = $request->mobile;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        if (SafeSchema::hasColumn('users', 'pincode')) {
            $user->pincode = $request->input('pincode', $request->zip);
        }
        $user->country_name = $request->country;
        $user->country_code = $request->country_code;
        if (SafeSchema::hasColumn('users', 'country')) {
            $user->country = $request->country ?: 'India';
        }
        if (SafeSchema::hasColumn('users', 'preferred_state')) {
            $user->preferred_state = $request->state;
        }
        if (SafeSchema::hasColumn('users', 'preferred_city')) {
            $user->preferred_city = $request->city;
        }
        if ($request->has('short_bio')) {
            $user->about = $request->short_bio;
        }

        if ($user->step < 1) {
            $user->step = 1;
        }
        $user->save();

        $existing = $user->studentProfile;
        $oldResume = $existing?->resume_path;
        $resumePath = $oldResume;
        if ($request->hasFile('resume')) {
            try {
                $resumePath = fileUploader($request->resume, getFilePath('studentResume'), null, $oldResume);
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Could not upload resume. Please use a PDF under 5 MB.')];

                return back()->withNotify($notify)->withInput();
            }
        }

        $domains = array_values(array_unique($request->input('preferred_domains', [])));

        $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'qualification' => $request->qualification,
                'education_status' => $request->education_status,
                'preferred_domains' => $domains,
                'preferred_state' => $request->state,
                'preferred_city' => $request->city,
                'resume_path' => $resumePath,
            ]
        );

        $notify[] = ['success', __('Basic information saved. Continue to skills.')];

        return to_route('user.profile.skill')->withNotify($notify);
    }

    public function education()
    {
        $pageTitle = __('Education history');
        $user = auth()->user();
        $educations = $user->educations;

        return view('Template::user.profile.education', compact('pageTitle', 'educations'));
    }

    public function submitEducations(Request $request)
    {
        $request->validate([
            'education.*.school' => 'required|string|max:255',
            'education.*.year_from' => 'nullable|string',
            'education.*.year_to' => 'nullable|string',
            'education.*.degree' => 'nullable|string|max:255',
            'education.*.area_of_study' => 'nullable|string|max:255',
            'education.*.description' => 'nullable|string',
        ], [
            'education.*.school.required' => 'The school name is required.',
        ]);

        $user = auth()->user();
        $updatedIds = [];

        if (! $request->education || empty($request->education)) {
            return back()->with('error', 'Please provide your education qualifications.');
        }

        foreach ($request->education as $educationData) {
            if (! empty($educationData['id'])) {
                $education = $user->educations()->find($educationData['id']);
                if ($education) {
                    $education->school = $educationData['school'];
                    $education->year_from = $educationData['year_from'];
                    $education->year_to = $educationData['year_to'];
                    $education->degree = $educationData['degree'];
                    $education->area_of_study = $educationData['area_of_study'];
                    $education->description = $educationData['description'];
                    $education->save();
                    $updatedIds[] = $education->id;
                }
            } else {
                $education = new Education;
                $education->user_id = $user->id;
                $education->school = $educationData['school'];
                $education->year_from = $educationData['year_from'];
                $education->year_to = $educationData['year_to'];
                $education->degree = $educationData['degree'];
                $education->area_of_study = $educationData['area_of_study'];
                $education->description = $educationData['description'];
                $education->save();
                $updatedIds[] = $education->id;
            }
        }

        $user->educations()->whereNotIn('id', $updatedIds)->delete();

        if ($user->step < 3) {
            $user->step = 3;
            $user->save();
        }

        $notify[] = ['success', __('Education updated successfully.')];

        return redirect()->route('user.profile.bank')->withNotify($notify);
    }

    public function bankDetails()
    {
        if (! SafeSchema::hasColumn('users', 'upi_id')) {
            $notify[] = ['warning', __('Bank details are not available yet. Ask the administrator to run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = __('Bank details');
        $user = auth()->user();

        return view('Template::user.profile.bank', compact('pageTitle', 'user'));
    }

    public function submitBankDetails(Request $request)
    {
        if (! SafeSchema::hasColumn('users', 'upi_id')) {
            $notify[] = ['warning', __('Bank details are not available yet. Ask the administrator to run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $request->validate([
            'bank_name' => 'nullable|string|max:191',
            'bank_account_number' => 'nullable|string|max:64',
            'account_number' => 'nullable|string|max:64',
            'bank_ifsc' => 'nullable|string|max:32',
            'ifsc_code' => 'nullable|string|max:32',
            'bank_account_holder_name' => 'nullable|string|max:191',
            'account_holder_name' => 'nullable|string|max:191',
            'upi_id' => 'nullable|string|max:255',
        ]);

        $clean = static fn ($v) => ($v === null || $v === '' || trim((string) $v) === '') ? null : trim((string) $v);

        $user = auth()->user();
        $user->bank_name = $clean($request->bank_name);
        $acct = $clean($request->bank_account_number);
        if ($acct === null) {
            $acct = $clean($request->account_number);
        }
        $user->bank_account_number = $acct;
        if (SafeSchema::hasColumn('users', 'account_number')) {
            $user->account_number = $acct;
        }
        $ifscRaw = $clean($request->bank_ifsc);
        if ($ifscRaw === null) {
            $ifscRaw = $clean($request->ifsc_code);
        }
        $ifsc = $ifscRaw !== null ? strtoupper($ifscRaw) : null;
        $user->bank_ifsc = $ifsc;
        if (SafeSchema::hasColumn('users', 'ifsc_code')) {
            $user->ifsc_code = $ifsc;
        }
        $holder = $clean($request->bank_account_holder_name);
        if ($holder === null) {
            $holder = $clean($request->account_holder_name);
        }
        $user->bank_account_holder_name = $holder;
        if (SafeSchema::hasColumn('users', 'account_holder_name')) {
            $user->account_holder_name = $holder;
        }
        $user->upi_id = $clean($request->upi_id);
        $user->save();

        $notify[] = ['success', __('Bank details saved.')];

        return to_route('user.profile.portfolio')->withNotify($notify);
    }

    /**
     * Step 3 — work samples only (no skills / bio here).
     */
    public function portfolio()
    {
        $user = auth()->user();
        if ($user->step < 2) {
            $notify[] = ['info', __('Complete skills before adding portfolio items.')];

            return to_route('user.profile.skill')->withNotify($notify);
        }

        $pageTitle = __('Work samples');
        $portfolios = $user->portfolios()->paginate(getPaginate());

        return view('Template::user.profile.portfolio', compact('pageTitle', 'user', 'portfolios'));
    }

    public function submitPortfolios(Request $request, $id = 0)
    {
        $user = auth()->user();
        $imageRule = $id ? 'nullable' : 'required';

        $request->validate([
            'title' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'description' => 'required|string',
            'project_url' => 'nullable|url|max:512',
            'image' => [$imageRule, new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        if ($id) {
            $portfolio = Portfolio::where('user_id', $user->id)->findOrFail($id);
            $notification = 'Portfolio updated successfully';
        } else {
            $portfolio = new Portfolio;
            $notification = 'Portfolio added successfully';
        }

        if ($request->hasFile('image')) {
            try {
                $portfolio->image = fileUploader($request->image, getFilePath('portfolio'), getFileSize('portfolio'), @$portfolio->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload portfolio image'];

                return back()->withNotify($notify);
            }
        }

        $portfolio->user_id = $user->id;
        $portfolio->title = $request->title;
        $portfolio->role = $request->role;
        $portfolio->description = $request->description;
        $portfolio->url = $request->project_url;
        if (SafeSchema::hasColumn('portfolios', 'skill_ids')) {
            $portfolio->skill_ids = [];
        }
        if (! $portfolio->exists) {
            $portfolio->status = Status::ENABLE;
        }
        $portfolio->save();

        if ($user->step < 4) {
            $user->step = 4;
            $user->save();
        }

        $notify[] = ['success', $notification];

        return back()->withNotify($notify);
    }

    public function statusPortfolio($id)
    {
        return Portfolio::changeStatus($id);
    }

    public function workProfileComplete()
    {
        $user = auth()->user();

        return User::changeStatus($user->id, 'work_profile_complete');
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator;
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username.'@'.gs('site_name'), $secret);
        $pageTitle = '2FA Security';

        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];

            return back()->withNotify($notify);
        }
        $notify[] = ['error', 'Wrong verification code'];

        return back()->withNotify($notify);
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }

        return back()->withNotify($notify);
    }
}
