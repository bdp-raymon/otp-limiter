<?php

namespace Raymon\OtpLimiter;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Raymon\OtpLimiter\Skeleton\SkeletonClass
 */
class OtpLimiterFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'otp-limiter';
    }
}
