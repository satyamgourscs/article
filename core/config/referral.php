<?php

return [
    'signup_bonus_amount' => (float) env('REFERRAL_SIGNUP_BONUS', 50),
    'currency' => env('REFERRAL_BONUS_NOTE', 'platform credits'),
];
