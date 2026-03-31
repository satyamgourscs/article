<?php

namespace App\Models;

use App\Constants\Status;
use App\Services\SubscriptionService;
use App\Support\SafeSchema;
use App\Traits\GlobalStatus;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify, GlobalStatus;

    /** @var list<string> */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'dial_code', 'mobile',
        'country_name', 'country_code', 'country', 'city', 'state', 'zip', 'pincode',
        'address', 'about', 'tagline', 'image', 'skill_ids', 'skills',
        'preferred_state', 'preferred_city',
        'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_holder_name', 'upi_id',
        'account_number', 'ifsc_code', 'account_holder_name',
        'referral_code', 'referred_by', 'referred_by_user_id',
        'profile_complete', 'work_profile_complete', 'step', 'badge_setting_id',
        'ev', 'sv', 'kv', 'status', 'ts', 'tv', 'tsc', 'language',
        'cv',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance',
        'kyc_data',
        'bank_account_number',
        'bank_ifsc',
        'upi_id',
        'account_number',
        'ifsc_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'kyc_data'          => 'object',
        'ver_code_send_at'  => 'datetime',
        'language'          => 'object',
        'skill_ids'         => 'array',
        'skills'            => 'array',
    ];
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user', 'user_id', 'skill_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->where('work_profile_complete', Status::YES);
    }
    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }
    public function scopeIncompleteProfile($query)
    {
        return $query->where('work_profile_complete', Status::NO);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeKycUnverified($query)
    {
        return $query->where('kv', Status::KYC_UNVERIFIED);
    }

    public function scopeKycPending($query)
    {
        return $query->where('kv', Status::KYC_PENDING);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }


    public function updateBadge()
    {
        $badge = BadgeSetting::where('min_amount', '<=', $this->earning)->orderBy('min_amount', 'desc')->first();
        if ($badge && $badge->id != $this->badge_setting_id) {
            $this->badge_setting_id = $badge->id;
            $this->save();
        }
    }
    
    public function badge()
    {
        return $this->belongsTo(BadgeSetting::class, 'badge_setting_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)->where('is_active', 1);
    }

    public function walletModel()
    {
        return $this->hasOne(Wallet::class);
    }

    public function walletTransactionsModel()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function walletWithdrawRequests()
    {
        return $this->hasMany(WalletWithdrawRequest::class);
    }

    public function portalJobApplications()
    {
        return $this->hasMany(JobApplication::class, 'user_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * Optional row in bank_details (legacy key name buyer_id often stores the same user id).
     */
    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class, 'buyer_id', 'id');
    }

    /**
     * Profile wizard / dashboard completion (0–100). Matches publish when profile_complete is set.
     */
    public function getProfileCompletionAttribute(): int
    {
        if ((int) $this->profile_complete === Status::YES) {
            return 100;
        }

        $progress = 0;

        $fn = trim((string) ($this->firstname ?? ''));
        $ln = trim((string) ($this->lastname ?? ''));
        $em = trim((string) ($this->email ?? ''));
        if ($fn !== '' && $ln !== '' && $em !== '' && filter_var($em, FILTER_VALIDATE_EMAIL)) {
            $progress += 20;
        }

        $skillIds = is_array($this->skill_ids) ? array_filter($this->skill_ids) : [];
        if (count($skillIds) > 0) {
            $progress += 20;
        } elseif ($this->relationLoaded('skills')) {
            if ($this->skills->isNotEmpty()) {
                $progress += 20;
            }
        } elseif ($this->skills()->exists()) {
            $progress += 20;
        }

        $portfolioCount = $this->portfolios_count ?? null;
        if ($portfolioCount === null) {
            $portfolioCount = $this->portfolios()->count();
        }
        if ($portfolioCount > 0) {
            $progress += 20;
        }

        $educationCount = $this->educations_count ?? null;
        if ($educationCount === null) {
            $educationCount = $this->educations()->count();
        }
        if ($educationCount > 0) {
            $progress += 20;
        }

        if ($this->hasProfileBankStepComplete()) {
            $progress += 20;
        }

        return min(100, $progress);
    }

    /**
     * Bank step for profile progress: users-table payout fields and/or bank_details row.
     */
    public function hasProfileBankStepComplete(): bool
    {
        if (SafeSchema::hasColumn('users', 'upi_id')
            || SafeSchema::hasColumn('users', 'bank_account_number')
            || SafeSchema::hasColumn('users', 'bank_name')) {
            return $this->hasWithdrawalPayoutDetails();
        }

        if (SafeSchema::hasTable('bank_details')) {
            try {
                return $this->bankDetail()->exists();
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    /**
     * Payout feature exists in schema (users columns and/or bank_details), not whether the user filled it.
     */
    public static function studentBankFormSupported(): bool
    {
        if (SafeSchema::hasTable('bank_details')) {
            return true;
        }

        return SafeSchema::hasColumn('users', 'upi_id')
            || SafeSchema::hasColumn('users', 'bank_account_number')
            || SafeSchema::hasColumn('users', 'bank_name')
            || SafeSchema::hasColumn('users', 'account_number')
            || SafeSchema::hasColumn('users', 'bank_ifsc');
    }

    /**
     * User has saved payout identifiers (dashboard / soft prompts). Safe if schema calls fail.
     */
    public function hasEnteredDashboardPayoutDetails(): bool
    {
        if (! self::studentBankFormSupported()) {
            return false;
        }

        try {
            $usersPayoutColumns = SafeSchema::hasColumn('users', 'upi_id')
                || SafeSchema::hasColumn('users', 'account_number')
                || SafeSchema::hasColumn('users', 'bank_account_number')
                || SafeSchema::hasColumn('users', 'bank_name');

            if ($usersPayoutColumns) {
                return $this->hasWithdrawalPayoutDetails();
            }

            if (SafeSchema::hasTable('bank_details')) {
                return $this->bankDetail()->exists();
            }
        } catch (\Throwable) {
            return false;
        }

        return false;
    }

    /**
     * True when user can request withdrawal: UPI id and/or bank account + IFSC are set.
     */
    public function hasWithdrawalPayoutDetails(): bool
    {
        $upi = trim((string) ($this->upi_id ?? ''));
        if ($upi !== '') {
            return true;
        }

        $account = trim((string) ($this->bank_account_number ?? $this->account_number ?? ''));
        $ifsc = trim((string) ($this->bank_ifsc ?? $this->ifsc_code ?? ''));
        if ($account !== '' && $ifsc !== '') {
            return true;
        }

        if (SafeSchema::hasTable('bank_details')) {
            try {
                $bd = $this->relationLoaded('bankDetail') ? $this->bankDetail : $this->bankDetail()->first();
                if (! $bd) {
                    return false;
                }
                $u = trim((string) ($bd->upi_id ?? ''));
                if ($u !== '') {
                    return true;
                }
                $a = trim((string) ($bd->account_number ?? ''));
                $i = trim((string) ($bd->ifsc ?? ''));

                return $a !== '' && $i !== '';
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    /**
     * Merged CV path: disk storage (cv/…) or legacy student_profiles.resume_path.
     */
    public function getCvAttribute($value): ?string
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        if ($this->relationLoaded('studentProfile')) {
            return $this->studentProfile?->resume_path;
        }

        return $this->studentProfile()->value('resume_path');
    }

    /**
     * Subscription tier for Blade checks (e.g. @if($user->plan !== 'free')).
     */
    public function getPlanAttribute(): string
    {
        return $this->isPaid() ? 'paid' : 'free';
    }

    public function isPaid(): bool
    {
        if (! SafeSchema::subscriptionsAvailable()) {
            return false;
        }
        $p = app(SubscriptionService::class)->getUserPlan($this->id);

        return $p !== null && (float) $p->price > 0.0;
    }

    /**
     * Public download URL for CV only if the file exists. Never exposes raw disk paths in UI.
     */
    public function getCvPublicUrlAttribute(): ?string
    {
        $rawCv = $this->attributes['cv'] ?? null;
        if (is_string($rawCv) && $rawCv !== '' && ! str_contains($rawCv, '..')) {
            if (str_starts_with($rawCv, 'cv/') && Storage::disk('public')->exists($rawCv)) {
                return Storage::disk('public')->url($rawCv);
            }
        }

        $profile = $this->relationLoaded('studentProfile')
            ? $this->studentProfile
            : $this->studentProfile()->first();
        $legacy = $profile?->resume_path;
        if (! is_string($legacy) || $legacy === '' || str_contains($legacy, '..')) {
            return null;
        }

        $relative = ltrim(str_replace('\\', '/', $legacy), '/');
        $full = realpath(public_path($relative));
        $publicRoot = realpath(public_path());
        if ($full === false || $publicRoot === false || ! str_starts_with($full, $publicRoot) || ! is_file($full)) {
            return null;
        }

        return asset($relative);
    }

    /**
     * Minimum content required before generating a PDF CV (avoids empty PDFs).
     */
    public function hasCvGenerationPayload(): bool
    {
        $this->loadMissing(['portfolios', 'educations', 'skills']);

        $hasName = trim((string) ($this->firstname ?? '')) !== '' || trim((string) ($this->lastname ?? '')) !== '';
        if (! $hasName) {
            return false;
        }

        $hasBio = trim(strip_tags((string) ($this->about ?? ''))) !== '';
        $hasSkills = $this->skills->isNotEmpty()
            || (is_array($this->skill_ids) && count(array_filter($this->skill_ids)) > 0);
        $hasEdu = $this->educations->isNotEmpty();
        $hasPortfolio = $this->portfolios->isNotEmpty();

        return $hasBio || $hasSkills || $hasEdu || $hasPortfolio;
    }

}
