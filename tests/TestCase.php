<?php

namespace Mmoollllee\LaravelConsentControl\Tests;

use Mmoollllee\LaravelConsentControl\LaravelConsentControlServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelConsentControlServiceProvider::class,
        ];
    }
}
