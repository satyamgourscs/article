<?php

/**
 * Fallback when general_settings.referral_signup_bonus is unavailable.
 * Admin: General Setting → Student referral bonus (preferred).
 */
return [
    'signup_bonus_amount' => (float) env('REFERRAL_SIGNUP_BONUS', 50),
    'currency' => env('REFERRAL_BONUS_NOTE', 'platform credits'),
];
