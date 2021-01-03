<?php


namespace BdpRaymon\OtpLimiter\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use BdpRaymon\OtpLimiter\OtpLimiter;

class OtpLimiterMiddleware
{

    protected OtpLimiter $otpLimiter;

    public function __construct(OtpLimiter $otpLimiter)
    {
        $this->otpLimiter = $otpLimiter;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param array ...$params
     * @throws Exception
     */
    public function handle(Request $request, Closure $next, ...$params): void
    {
        $key = $this->getKey($request, $params);

        if (!$key) {
            $next($request);
            return;
        }

        if ($this->otpLimiter->allowed($key)) {
            $this->otpLimiter->set($key);
            $next($request);
            return;
        }

        $this->otpLimiter->throw($key);
    }

    protected function getKey(Request $request, array $params): string
    {
        $key = '';
        foreach ($params as $param) {
            $key .= $request->input($param);
        }

        return $key;
    }

}
