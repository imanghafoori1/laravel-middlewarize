<?php

namespace Imanghafoori\MiddlewarizeTests;

use Illuminate\Support\Facades\Cache;
use Imanghafoori\Middlewarize\Middlewarable;
use Imanghafoori\Middlewarize\CacheMiddleware;

class MiddlewarizeTests extends TestCase
{
    public function testHello()
    {
        $r = new MyClass();
        Cache::shouldReceive('remember')->once()->andReturn('hello');
        $r->middleware(CacheMiddleware::class.':foo,6 seconds')->find(1);
    }

    public function testHello2()
    {
        Cache::shouldReceive('remember')->once()->andReturn('hello');

        $cacher = new CacheMiddleware2(function () {
            return 'foo';
        }, '1 second');

        (new MyClass())->middleware($cacher)->find(1);
    }
}

class MyClass 
{
    use Middlewarable;
    
    public function find($id)
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
