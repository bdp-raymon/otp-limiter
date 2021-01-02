<?php

return [
    // interval between each otp could be generate
    'otp-rate-limiter' => env('OTP_RATE_LIMITER', 60 * 3),

    // exception that will throw if otp limited
    'exception' => Exception::class
];
