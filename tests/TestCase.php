<?php

namespace Imanghafoori\MiddlewarizeTests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [];
    }
}
