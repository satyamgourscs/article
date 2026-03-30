<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\BuyerNotify;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Buyer extends Authenticatable
{
    use HasApiTokens, BuyerNotify;

    /** @var list<string> */
    protected $fillable = [
        'firstname', 'lastname', 'username', 'email', 'dial_code', 'mobile',
        'country_name', 'country_code', 'country', 'city', 'state', 'zip', 'pincode',
        'address', 'image', 'skills', 'preferred_state', 'preferred_city',
        'bank_name', 'bank_account_number', 'bank_ifsc', 'bank_account_holder_name', 'upi_id',
        'account_number', 'ifsc_code', 'account_holder_name',
        'referral_code', 'referred_by', 'referred_by_user_id',
        'profile_complete', 'ev', 'sv', 'kv', 'status', 'ts', 'tv', 'tsc', 'language',
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
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime',
        'language'     => 'object',
        'skills'       => 'array',
    ];


    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class)->orderBy('id', 'desc');
    }

    public function postedJobs()
    {
        return $this->hasMany(PostedJob::class, 'buyer_id')->orderByDesc('id');
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
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function buyerReviews()
    {
        return $this->hasMany(BuyerReview::class);
    }
    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => trim(($this->firstname ?? '') . ' ' . ($this->lastname ?? '')),
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => $this->dial_code ? $this->dial_code . $this->mobile : $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
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

    public function subscriptions()
    {
        return $this->hasMany(BuyerSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(BuyerSubscription::class)->where('is_active', 1);
    }

    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }
}
