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

    public function testItCanWorkForStaticMethods()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        MyClass::middlewared(CacheMiddleware::class.':foo1,6 seconds')->static_find(1);
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
