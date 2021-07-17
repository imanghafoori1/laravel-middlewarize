<?php


namespace Imanghafoori\Middlewarize\Tests\helpers\Middlewares;

use Illuminate\Support\Facades\Cache;

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
