<?php


namespace Imanghafoori\Middlewarize\Tests\helpers\Middlewares;

use Illuminate\Support\Facades\Cache;

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
