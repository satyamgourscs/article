<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\Buyer;
use App\Models\CompanyProfile;
use App\Models\OtpVerification;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Support\SafeSchema;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Session\Store;

class OtpAuthService
{
    public function normalizeContact(string $raw): array
    {
        $raw = trim($raw);
        if (filter_var($raw, FILTER_VALIDATE_EMAIL)) {
            return ['key' => strtolower($raw), 'email' => strtolower($raw), 'mobile' => null, 'dial_code' => null];
        }
        $digits = preg_replace('/\D+/', '', $raw);
        if (strlen($digits) < 8) {
            throw new \InvalidArgumentException(__('Enter a valid email or mobile number.'));
        }

        return ['key' => 'm:'.$digits, 'email' => null, 'mobile' => $digits, 'dial_code' => null];
    }

    public function findUserAccount(array $norm): ?User
    {
        if ($norm['email']) {
            return User::where('email', $norm['email'])->first();
        }

        return User::where('mobile', $norm['mobile'])
            ->orWhere(DB::raw("REPLACE(mobile, ' ', '')"), $norm['mobile'])
            ->first();
    }

    public function findBuyerAccount(array $norm): ?Buyer
    {
        if ($norm['email']) {
            return Buyer::where('email', $norm['email'])->first();
        }

        return Buyer::where('mobile', $norm['mobile'])->first();
    }

    public function findAdminByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }

    public function sendOtp(string $contactRaw, string $guardTarget): OtpVerification
    {
        $norm = $this->normalizeContact($contactRaw);
        $key = $norm['key'];

        if ($guardTarget === 'admin') {
            throw new \RuntimeException(__('Admin does not use OTP. Sign in at /admin with your password.'));
        }

        if (in_array($guardTarget, ['web', 'buyer'], true) && config('otp.test_mode')) {
            Log::info('OTP TEST MODE: send skipped; use static code from config/otp.php', ['contact' => $key, 'guard' => $guardTarget]);

            return new OtpVerification([
                'contact' => $key,
                'otp' => (string) config('otp.test_code'),
                'expires_at' => Carbon::now()->addHour(),
                'is_used' => false,
                'verify_attempts' => 0,
                'guard_target' => $guardTarget,
            ]);
        }

        if (! SafeSchema::otpVerificationsAvailable()) {
            throw new \RuntimeException(__('OTP sign-in is not available. Run database migrations (php artisan migrate).'));
        }

        OtpVerification::where('contact', $key)->where('is_used', false)->update(['is_used' => true]);

        $otp = (string) random_int(100000, 999999);
        $row = OtpVerification::create([
            'contact' => $key,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_used' => false,
            'verify_attempts' => 0,
            'guard_target' => $guardTarget,
        ]);

        $this->dispatchOtpMessage($norm, $otp);

        return $row;
    }

    protected function dispatchOtpMessage(array $norm, string $otp): void
    {
        $line = 'Your verification code is: '.$otp.' (valid 5 minutes).';
        if ($norm['email']) {
            try {
                Mail::raw($line, function ($message) use ($norm) {
                    $message->to($norm['email'])->subject(config('app.name').' — Login code');
                });
            } catch (\Throwable $e) {
                Log::warning('OTP mail failed: '.$e->getMessage());
            }
        }
        Log::info('OTP dispatched', ['to' => $norm['key'], 'otp' => $otp]);
    }

    /** @return array{action:string, user?:User, buyer?:Buyer, norm?:array, guard_target?:string} */
    public function verifyOtp(string $contactRaw, string $otpCode, string $guardTarget): array
    {
        $norm = $this->normalizeContact($contactRaw);

        if ($guardTarget === 'admin') {
            throw new \RuntimeException(__('Admin sign-in uses email/username and password on /admin only.'));
        }

        if (in_array($guardTarget, ['web', 'buyer'], true) && config('otp.test_mode')) {
            if (! hash_equals((string) config('otp.test_code'), trim((string) $otpCode))) {
                throw new \RuntimeException(__('Invalid OTP.'));
            }

            return $this->memberLoginOrRegisterOutcome($norm, $guardTarget);
        }

        if (! SafeSchema::otpVerificationsAvailable()) {
            throw new \RuntimeException(__('OTP sign-in is not available. Run database migrations (php artisan migrate).'));
        }

        $key = $norm['key'];

        $record = OtpVerification::where('contact', $key)
            ->where('is_used', false)
            ->where('guard_target', $guardTarget)
            ->orderByDesc('id')
            ->first();

        if (! $record) {
            throw new \RuntimeException(__('No active code. Request a new OTP.'));
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            throw new \RuntimeException(__('OTP expired. Request a new one.'));
        }

        if ($record->verify_attempts >= OtpVerification::MAX_VERIFY_ATTEMPTS) {
            throw new \RuntimeException(__('Too many attempts. Request a new OTP.'));
        }

        if (! hash_equals($record->otp, trim((string) $otpCode))) {
            $record->increment('verify_attempts');
            throw new \RuntimeException(__('Invalid OTP.'));
        }

        $record->update(['is_used' => true]);

        return $this->memberLoginOrRegisterOutcome($norm, $guardTarget);
    }

    /** @return array{action:string, user?:User, buyer?:Buyer, norm:array, guard_target?:string} */
    protected function memberLoginOrRegisterOutcome(array $norm, string $guardTarget): array
    {
        $user = $this->findUserAccount($norm);
        $buyer = $this->findBuyerAccount($norm);

        if ($user && $buyer) {
            return $guardTarget === 'web'
                ? ['action' => 'login', 'user' => $user, 'norm' => $norm]
                : ['action' => 'login', 'buyer' => $buyer, 'norm' => $norm];
        }
        if ($user && ! $buyer) {
            if ($guardTarget === 'buyer') {
                throw new \RuntimeException(__('This contact is registered as a student. Use student login.'));
            }

            return ['action' => 'login', 'user' => $user, 'norm' => $norm];
        }
        if ($buyer && ! $user) {
            if ($guardTarget === 'web') {
                throw new \RuntimeException(__('This contact is registered as a company account. Use company login.'));
            }

            return ['action' => 'login', 'buyer' => $buyer, 'norm' => $norm];
        }

        return ['action' => 'register', 'norm' => $norm, 'guard_target' => $guardTarget];
    }

    /**
     * Signup wizard payload stored before OTP; must match verified contact.
     *
     * @return array<string, mixed>|null
     */
    public function signupProfileForContactFromSession(Store $session, string $guardTarget, string $contactKey): ?array
    {
        if ($guardTarget === 'web') {
            $raw = $session->get('otp_signup_student');
        } elseif ($guardTarget === 'buyer') {
            $raw = $session->get('otp_signup_company');
        } else {
            return null;
        }

        if (! is_array($raw) || (($raw['_contact_key'] ?? '') !== $contactKey)) {
            return null;
        }

        return Arr::except($raw, ['_contact_key']);
    }

    public function normFromContactKey(string $key): array
    {
        $key = trim($key);
        if (str_starts_with($key, 'm:')) {
            $digits = substr($key, 2);

            return ['key' => $key, 'email' => null, 'mobile' => $digits, 'dial_code' => null];
        }

        return ['key' => strtolower($key), 'email' => strtolower($key), 'mobile' => null, 'dial_code' => null];
    }

    public function resolveEmailForNewAccount(array $norm): string
    {
        if ($norm['email']) {
            return $norm['email'];
        }

        return 'm'.$norm['mobile'].'@sms.'.parse_url(config('app.url'), PHP_URL_HOST ?: 'local');
    }

    public function uniqueUsername(string $base, string $table = 'users'): string
    {
        $base = Str::slug(str_replace('@', '_', $base), '_');
        if ($base === '' || strlen($base) < 2) {
            $base = 'user';
        }
        $base = substr($base, 0, 32);
        for ($i = 0; ; $i++) {
            $candidate = $i === 0 ? $base : substr($base, 0, 28).'_'.$i;
            $candidate = substr($candidate, 0, 40);
            $exists = $table === 'buyers'
                ? Buyer::where('username', $candidate)->exists()
                : User::where('username', $candidate)->exists();
            if (! $exists) {
                return $candidate;
            }
        }
    }

    /**
     * Single-step signup: create User or Buyer right after OTP verification (no extra form).
     *
     * @param  array<string, mixed>|null  $signupProfile
     * @return User|Buyer
     */
    public function createAccountFromOtpVerify(array $norm, string $guardTarget, ?array $signupProfile)
    {
        $profile = $signupProfile ?? [];
        $firstname = trim((string) ($profile['firstname'] ?? ''));
        $lastname = trim((string) ($profile['lastname'] ?? ''));
        if ($firstname === '' || $lastname === '') {
            [$firstname, $lastname] = $this->placeholderNamesFromNorm($norm);
        }

        $referralInput = isset($profile['referral_code']) && $profile['referral_code'] !== ''
            ? trim((string) $profile['referral_code'])
            : null;

        if ($guardTarget === 'web' && $referralInput !== null && $referralInput !== '' && SafeSchema::hasColumn('users', 'referral_code')) {
            $ref = strtoupper($referralInput);
            if (! User::where('referral_code', $ref)->exists()) {
                throw new \RuntimeException(__('Invalid referral code.'));
            }
        }

        $extras = Arr::except($profile, ['firstname', 'lastname', 'referral_code']);

        if ($guardTarget === 'web') {
            return $this->registerStudentAfterOtp($norm, $firstname, $lastname, $referralInput, $extras !== [] ? $extras : null);
        }
        if ($guardTarget === 'buyer') {
            return $this->registerFirmAfterOtp($norm, $firstname, $lastname, $extras !== [] ? $extras : null);
        }

        throw new \RuntimeException(__('Invalid registration channel.'));
    }

    /**
     * @return array{0: string, 1: string}
     */
    public function placeholderNamesFromNorm(array $norm): array
    {
        $email = strtolower($this->resolveEmailForNewAccount($norm));
        $local = explode('@', $email, 2)[0] ?? 'user';
        $local = preg_replace('/[^a-z0-9._-]+/i', '', $local) ?? '';
        if ($local === '' || strlen($local) < 2) {
            $local = 'user';
        }
        $firstname = Str::title(str_replace(['.', '_', '-'], ' ', $local));
        $firstname = substr($firstname, 0, 80);

        return [$firstname, 'Member'];
    }

    /** @throws \RuntimeException */
    public function registerStudentAfterOtp(array $norm, string $firstname, string $lastname, ?string $referralCode, ?array $signupExtras = null): User
    {
        if ($this->findUserAccount($norm) || $this->findBuyerAccount($norm)) {
            throw new \RuntimeException(__('An account already exists for this contact.'));
        }

        if (! gs('registration')) {
            throw new \RuntimeException(__('Registration is not allowed.'));
        }

        $email = strtolower($this->resolveEmailForNewAccount($norm));
        if (User::where('email', $email)->exists() || Buyer::where('email', $email)->exists()) {
            throw new \RuntimeException(__('Could not create account: please use a different email or contact support.'));
        }

        return DB::transaction(function () use ($norm, $firstname, $lastname, $referralCode, $email, $signupExtras) {
            $user = new User;
            $user->firstname = $firstname;
            $user->lastname = $lastname;
            $user->email = $email;
            $user->username = $this->uniqueUsername($norm['email'] ? explode('@', $norm['email'])[0] : ('m'.$norm['mobile']), 'users');
            $user->mobile = $norm['mobile'];
            $user->dial_code = $norm['dial_code'];
            $user->password = self::randomUnusedPasswordHash();
            $user->kv = gs('kv') ? Status::NO : Status::YES;
            $user->ev = $norm['email'] ? Status::VERIFIED : (gs('ev') ? Status::NO : Status::YES);
            $user->sv = $norm['mobile'] ? Status::VERIFIED : (gs('sv') ? Status::NO : Status::YES);
            $user->ts = Status::DISABLE;
            $user->tv = Status::ENABLE;
            if (SafeSchema::hasColumn('users', 'profile_complete')) {
                $user->profile_complete = Status::YES;
            }
            $hasReferral = SafeSchema::usersReferralReady();
            if ($hasReferral) {
                $user->referral_code = $this->generateUniqueReferralCode();
            }
            $user->save();

            $referrer = null;
            if ($hasReferral && $referralCode) {
                $referrer = User::where('referral_code', strtoupper(trim($referralCode)))
                    ->where('id', '!=', $user->id)
                    ->first();
                if ($referrer) {
                    $user->referred_by_user_id = $referrer->id;
                    $user->save();
                }
            }

            if (SafeSchema::walletCoreAvailable()) {
                Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
                );
            }

            $bonus = (float) config('referral.signup_bonus_amount', 0);
            if ($referrer && $bonus > 0 && SafeSchema::walletCoreAvailable()) {
                $this->creditUserWallet($user->id, $bonus, WalletTransaction::TYPE_CREDIT, 'referral', ['referrer_id' => $referrer->id]);
            }

            $adminNotification = new AdminNotification;
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New student registered';
            $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
            $adminNotification->save();

            event(new Registered($user));

            try {
                app(SubscriptionService::class)->assignFreePlanToUser($user->id);
            } catch (\Throwable $e) {
                Log::error('OTP register student free plan: '.$e->getMessage());
            }

            if ($signupExtras && SafeSchema::hasTable('student_profiles')) {
                $eduStatuses = config('student_profile.education_statuses', []);
                $quals = config('student_profile.qualifications', []);
                $statusKey = $eduStatuses ? array_key_first($eduStatuses) : 'completed';
                $qualKey = $quals ? array_key_first($quals) : 'graduate';
                StudentProfile::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'qualification' => $signupExtras['qualification'] ?? $qualKey,
                        'education_status' => $signupExtras['education_status'] ?? $statusKey,
                        'preferred_domains' => $signupExtras['preferred_domains'] ?? [],
                        'preferred_state' => $signupExtras['preferred_state'] ?? null,
                        'preferred_city' => $signupExtras['preferred_city'] ?? null,
                    ]
                );
            }

            return $user;
        });
    }

    /** @throws \RuntimeException */
    public function registerFirmAfterOtp(array $norm, string $firstname, string $lastname, ?array $signupExtras = null): Buyer
    {
        if ($this->findUserAccount($norm) || $this->findBuyerAccount($norm)) {
            throw new \RuntimeException(__('An account already exists for this contact.'));
        }

        if (! gs('buyer_registration')) {
            throw new \RuntimeException(__('Registration is not allowed.'));
        }

        $email = strtolower($this->resolveEmailForNewAccount($norm));
        if (User::where('email', $email)->exists() || Buyer::where('email', $email)->exists()) {
            throw new \RuntimeException(__('Could not create account: please use a different email or contact support.'));
        }

        return DB::transaction(function () use ($norm, $firstname, $lastname, $email, $signupExtras) {
            $buyer = new Buyer;
            $buyer->firstname = $firstname;
            $buyer->lastname = $lastname;
            $buyer->email = $email;
            $buyer->username = $this->uniqueUsername($norm['email'] ? explode('@', $norm['email'])[0] : ('m'.$norm['mobile']), 'buyers');
            $buyer->mobile = $norm['mobile'];
            $buyer->dial_code = $norm['dial_code'];
            $buyer->password = self::randomUnusedPasswordHash();
            $buyer->kv = gs('kv') ? Status::NO : Status::YES;
            $buyer->ev = $norm['email'] ? Status::VERIFIED : (gs('ev') ? Status::NO : Status::YES);
            $buyer->sv = $norm['mobile'] ? Status::VERIFIED : (gs('sv') ? Status::NO : Status::YES);
            $buyer->ts = Status::DISABLE;
            $buyer->tv = Status::ENABLE;
            if (SafeSchema::hasColumn('buyers', 'profile_complete')) {
                $buyer->profile_complete = Status::YES;
            }
            $buyer->save();

            $adminNotification = new AdminNotification;
            $adminNotification->buyer_id = $buyer->id;
            $adminNotification->title = 'New firm registered';
            $adminNotification->click_url = urlPath('admin.buyers.detail', $buyer->id);
            $adminNotification->save();

            event(new Registered($buyer));

            try {
                app(SubscriptionService::class)->assignFreePlanToBuyer($buyer->id);
            } catch (\Throwable $e) {
                Log::error('OTP register firm free plan: '.$e->getMessage());
            }

            if ($signupExtras && SafeSchema::hasTable('company_profiles') && ! empty($signupExtras['firm_name']) && ! empty($signupExtras['firm_type'])) {
                CompanyProfile::updateOrCreate(
                    ['buyer_id' => $buyer->id],
                    [
                        'firm_name' => $signupExtras['firm_name'],
                        'firm_type' => $signupExtras['firm_type'],
                        'state' => $signupExtras['state'] ?? null,
                        'city' => $signupExtras['city'] ?? null,
                    ]
                );
            }

            return $buyer;
        });
    }

    public function creditUserWallet(int $userId, float $amount, string $type, string $source, array $meta = []): void
    {
        if ($amount <= 0 || ! SafeSchema::walletCoreAvailable()) {
            return;
        }

        DB::transaction(function () use ($userId, $amount, $type, $source, $meta) {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
            );
            if ($type === WalletTransaction::TYPE_CREDIT) {
                $wallet->balance = (float) $wallet->balance + $amount;
                $wallet->total_earned = (float) $wallet->total_earned + $amount;
            } else {
                $wallet->balance = max(0, (float) $wallet->balance - $amount);
                $wallet->total_withdrawn = (float) $wallet->total_withdrawn + $amount;
            }
            $wallet->save();

            WalletTransaction::create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $type,
                'source' => $source,
                'meta' => $meta ?: null,
            ]);
        });
    }

    public static function randomUnusedPasswordHash(): string
    {
        return bcrypt(Str::random(48));
    }

    public function generateUniqueReferralCode(): string
    {
        if (! SafeSchema::hasColumn('users', 'referral_code')) {
            return strtoupper(Str::random(8));
        }

        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
