<?php

/**
 * --------------------------------------------------------------------------
 * WARNING: TEST MODE — Student/Firm OTP authentication
 * --------------------------------------------------------------------------
 * When otp.test_mode is true, only the static otp.test_code is accepted for
 * student (web) and firm (buyer) login. Turn off in production and use real OTP.
 */
$otpTestCode = env('OTP_TEST_CODE', '123456');

return [
    'test_mode' => (bool) env('OTP_TEST_MODE', true),
    'test_code' => $otpTestCode,
    'test_banner' => 'THIS IS TEST MODE — student/firm login accepts only OTP '.$otpTestCode.'. Set OTP_TEST_MODE=false in production.',
];
