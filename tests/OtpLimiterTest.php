<?php


namespace Raymon\OtpLimiter\Tests;


use Orchestra\Testbench\TestCase;
use Raymon\OtpLimiter\OtpLimiter;
use Raymon\OtpLimiter\OtpLimiterServiceProvider;

class OtpLimiterTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [OtpLimiterServiceProvider::class];
    }

    protected function otpLimiter(): OtpLimiter
    {
        return $this->app->make('otp-limiter');
    }

    public function test_otp_could_be_limited_for_specific_key()
    {
        $key = 'test';
        $this->assertTrue($this->otpLimiter()->allowed($key));

        $this->otpLimiter()->set($key);

        $this->assertFalse($this->otpLimiter()->allowed($key));
    }

    public function test_limiter_rate()
    {
        $key = 'test';
        $this->otpLimiter()->set($key);
        $this->assertFalse($this->otpLimiter()->allowed($key));

        $this->travel((60 * 3) + 1)->seconds();

        $this->assertTrue($this->otpLimiter()->allowed($key));
    }

    public function test_remaining_time()
    {
        $key = 'test';
        $this->otpLimiter()->set($key);

        $this->travel(60 * 2)->seconds();

        $this->assertEquals(59, $this->otpLimiter()->remained($key));
    }

    public function test_throw_exception()
    {
        $this->expectException(\Exception::class);

        $this->otpLimiter()->throw('test');
    }
}
