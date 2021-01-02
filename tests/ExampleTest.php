<?php

namespace Raymon\OtpLimiter\Tests;

use Orchestra\Testbench\TestCase;
use Raymon\OtpLimiter\OtpLimiterServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [OtpLimiterServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
