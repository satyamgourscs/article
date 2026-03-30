<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\UserNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
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

        return $account !== '' && $ifsc !== '';
    }

}
