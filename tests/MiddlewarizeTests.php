<?php

namespace Imanghafoori\Middlewarize\Tests;

use Imanghafoori\Middlewarize\Tests\helpers\Middlewares\AdderMiddleware;
use Imanghafoori\Middlewarize\Tests\helpers\Middlewares\CacheMiddleware;
use Imanghafoori\Middlewarize\Tests\helpers\Middlewares\CacheMiddleware2;
use Imanghafoori\Middlewarize\Tests\helpers\Middlewares\MyMiddleware;
use Imanghafoori\Middlewarize\Tests\helpers\MockClass;
use InvalidArgumentException;
use Illuminate\Support\Facades\Cache;
use Imanghafoori\Middlewarize\Middlewarable;

class MiddlewarizeTests extends TestCase
{
    public function testHello()
    {
        $r = new MockClass();
        Cache::shouldReceive('remember')->once()->andReturn('hello');
        $r->middleware(CacheMiddleware::class.':foo,6 seconds')->find(1);
    }

    public function testItCanWorkWithObjectMiddlewares()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        $cacher = new CacheMiddleware2(function () {
            return 'foo';
        }, '1 second');

        (new MockClass())->middleware($cacher)->find(1);
    }

    public function testItWillCallTheActualStaticMethod()
    {
        $value = MockClass::middlewared(CacheMiddleware::class.':foo2,0 seconds')->static_find(1);

        $this->assertEquals($value, 1);
    }

    public function testItWillCallTheActualMethod()
    {
        $value = (new MockClass())->middleware(CacheMiddleware::class.':foo2,0 seconds')->find(1);

        $this->assertEquals($value, 1);
    }

    public function testItCanHaveMultipleMiddlewares()
    {
        $value = (new MockClass())->middleware([
            AdderMiddleware::class,
            AdderMiddleware::class,
            AdderMiddleware::class,
        ])->find(1);

        $this->assertEquals($value, 4);

        $value = (new MockClass())->middleware([
            [new AdderMiddleware, 'handle2'],
            [new AdderMiddleware, 'handle'],
            [AdderMiddleware::class, 'handle3'],
        ])->find(1);

        $this->assertEquals($value, 4);

        $value = (new MockClass())->middleware(
            [AdderMiddleware::class, 'handle3']
        )->find(1);
        $this->assertEquals($value, 2);
    }

    public function testItCanCallOtherMethodsAsMiddlewares()
    {
        $value = (new MockClass())->middleware([
            AdderMiddleware::class.'@handle2',
            AdderMiddleware::class.'@handle2',
            AdderMiddleware::class,
        ])->find(1);

        $this->assertEquals($value, 4);

        $r = new MockClass();
        Cache::shouldReceive('remember')->once()->andReturn('hello');
        $r->middleware(CacheMiddleware::class.'@handle:foo,6 seconds')->find(1);
    }

    public function testItCanWorkForStaticMethods()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        MockClass::middlewared(CacheMiddleware::class.':foo1,6 seconds')->static_find(1);
    }

    public function testItCanWorkForClosures()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        $handle = function ($data, $next) {
            $ttl = \DateInterval::createFromDateString('6 seconds');

            return Cache::remember('user', $ttl, function () use ($next, $data) {
                return $next($data);
            });
        };

        MockClass::middlewared($handle)->static_find(1);
    }

    public function testItExecutesMiddlewaresInCaseOfException()
    {
        Cache::shouldReceive('forget')->once()->with('Oh my God');

        $value = (new MockClass())->middleware([
            MyMiddleware::class
        ])->faily(1);

        $this->assertEquals('Oh my God1q2w3e', $value);
    }

    public function testItExecutesMiddlewaresInCaseOfStaticException()
    {
        Cache::shouldReceive('forget')->once()->with('Oh my God');

        $value = MockClass::middlewared([
            MyMiddleware::class
        ])->static_faily(1);

        $this->assertEquals('Oh my God1q2w3e', $value);
    }

    public function testExceptionIsRethrown()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Oh my God');

        MockClass::middlewared(function ($data, $next) {
            return $next($data);
        })->static_faily(1);
    }

    public function testItThrowsInvalidArgumentExceptionWhenPipeIsOfSillyTypes2()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A pipe must be an object, a string or a callable. array given');

        $value = MockClass::middlewared([[]])->static_find(1);

        $this->assertEquals($value, 1);
    }
}
