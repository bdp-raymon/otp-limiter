<?php


namespace BdpRaymon\OtpLimiter\Tests;


use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use BdpRaymon\OtpLimiter\Exceptions\OtpRequestExceededException;
use BdpRaymon\OtpLimiter\Middleware\OtpLimiterMiddleware;
use BdpRaymon\OtpLimiter\OtpLimiter;
use BdpRaymon\OtpLimiter\OtpLimiterServiceProvider;

class MiddlewareTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [OtpLimiterServiceProvider::class];
    }

    protected function otpLimiter(): OtpLimiter
    {
        return $this->app->make('otp-limiter');
    }

    public function test_middleware_work_correctly()
    {
        $request = new Request();
        $request->merge(['username' => 'test']);

        $middleware = new OtpLimiterMiddleware($this->otpLimiter());

        $response = $middleware->handle($request, function ($request) {}, 'username');

        $this->assertNull($response);
    }

    public function test_middleware_throw_exeption_if_otp_is_limited()
    {
        $request = new Request();
        $request->merge(['username' => 'test']);
        $this->otpLimiter()->set('test');

        $middleware = new OtpLimiterMiddleware($this->otpLimiter());

        $this->expectException(OtpRequestExceededException::class);

        $middleware->handle($request, function ($request) {}, 'username');
    }
}
