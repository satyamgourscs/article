<?php

namespace App\Http\Controllers\Concerns;

use App\Constants\Status;
use App\Models\Buyer;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;

trait RecordsOtpAuthenticatedLogin
{
    protected function recordUserLogin(Request $request, User $user): void
    {
        $user->tv = $user->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $user->save();

        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin;
        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude = @implode(',', $info['long']);
            $userLogin->latitude = @implode(',', $info['lat']);
            $userLogin->city = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country = @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }

    protected function recordBuyerLogin(Request $request, Buyer $buyer): void
    {
        $buyer->tv = $buyer->ts == Status::VERIFIED ? Status::UNVERIFIED : Status::VERIFIED;
        $buyer->save();

        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin;
        if ($exist) {
            $userLogin->longitude = $exist->longitude;
            $userLogin->latitude = $exist->latitude;
            $userLogin->city = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country = $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude = @implode(',', $info['long']);
            $userLogin->latitude = @implode(',', $info['lat']);
            $userLogin->city = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country = @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->buyer_id = $buyer->id;
        $userLogin->user_ip = $ip;
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }
}
