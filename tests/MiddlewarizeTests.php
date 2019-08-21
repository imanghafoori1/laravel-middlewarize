<?php

namespace Imanghafoori\MiddlewarizeTests;

use Illuminate\Support\Facades\Cache;
use Imanghafoori\Middlewarize\Middlewarable;

class MiddlewarizeTests extends TestCase
{
    public function testHello()
    {
        $r = new MyClass();
        Cache::shouldReceive('remember')->once()->andReturn('hello');
        $r->middleware(CacheMiddleware::class.':foo,6 seconds')->find(1);
    }

    public function testItCanWorkWithObjectMiddlewares()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        $cacher = new CacheMiddleware2(function () {
            return 'foo';
        }, '1 second');

        (new MyClass())->middleware($cacher)->find(1);
    }

    public function testItWillCallTheActualStaticMethod()
    {
        $value = MyClass::middlewared(CacheMiddleware::class.':foo2,0 seconds')->static_find(1);
         
        $this->assertEquals($value, 1);
    }

    public function testItWillCallTheActualMethod()
    {
        $value = (new MyClass())->middleware(CacheMiddleware::class.':foo2,0 seconds')->find(1);

        $this->assertEquals($value, 1);
    }

    public function testItCanHaveMultipleMiddlewares()
    {
        $value = (new MyClass())->middleware([
            AdderMiddleware::class,
            AdderMiddleware::class,
            AdderMiddleware::class,
        ])->find(1);

        $this->assertEquals($value, 4);
    }

    public function testItCanCallOtherMethodsAsMiddlewares()
    {
        $value = (new MyClass())->middleware([
            AdderMiddleware::class.'@handle2',
            AdderMiddleware::class.'@handle2',
            AdderMiddleware::class,
        ])->find(1);

        $this->assertEquals($value, 4);

        $r = new MyClass();
        Cache::shouldReceive('remember')->once()->andReturn('hello');
        $r->middleware(CacheMiddleware::class.'@handle:foo,6 seconds')->find(1);
    }

    public function testItCanWorkForStaticMethods()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        MyClass::middlewared(CacheMiddleware::class.':foo1,6 seconds')->static_find(1);
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

        MyClass::middlewared($handle)->static_find(1);
    }

    public function testItExecutesMiddlewaresInCaseOfException()
    {
        Cache::shouldReceive('forget')->once()->with('Oh my God');

        $value = (new MyClass())->middleware([
            MyMiddleware::class
        ])->faily(1);

        $this->assertEquals('Oh my God1q2w3e', $value);
    }

    public function testItExecutesMiddlewaresInCaseOfStaticException()
    {
        Cache::shouldReceive('forget')->once()->with('Oh my God');

        $value = MyClass::middlewared([
            MyMiddleware::class
        ])->static_faily(1);

        $this->assertEquals('Oh my God1q2w3e', $value);
    }
}

class MyClass 
{
    use Middlewarable;

    public function find($id)
    {
        return $id;
    }

    public static function static_find($id)
    {
        return $id;
    }

    public function faily()
    {
        throw new \Exception('Oh my God');
    }

    public static function static_faily()
    {
        throw new \Exception('Oh my God');
    }
}

class CacheMiddleware2
{
    private $keyMaker;

    private $ttl;

    /**
     * CacheMiddleware2 constructor.
     *
     * @param $keyMaker
     * @param $ttl
     */
    public function __construct($keyMaker, $ttl)
    {
        $this->keyMaker = $keyMaker;

        $this->ttl = $ttl;
    }

    public function handle($data, $next)
    {
        $ttl = \DateInterval::createFromDateString($this->ttl);

        $t = $this->keyMaker;

        return Cache::remember($t($data), $ttl, function () use ($next, $data) {
            return $next($data);
        });
    }
}

class CacheMiddleware
{
    public function handle($data, $next, $key, $ttl)
    {
        $ttl = \DateInterval::createFromDateString($ttl);
        return Cache::remember($key, $ttl, function () use ($next, $data) {
            return $next($data);
        });
    }
}

class AdderMiddleware
{
    public function handle($data, $next)
    {
        return $next($data) + 1;
    }

    public function handle2($data, $next)
    {
        return $next($data) + 1;
    }
}

class MyMiddleware
{
    public function handle($data, $next)
    {
        $resp = $next($data);
        Cache::forget($resp->getMessage());

        return $resp->getMessage(). '1q2w3e';
    }
}