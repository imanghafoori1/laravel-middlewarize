<?php


namespace Imanghafoori\Middlewarize\Tests\helpers\Middlewares;


use Illuminate\Support\Facades\Cache;

class MyMiddleware
{
    public function handle($data, $next)
    {
        $resp = $next($data);
        Cache::forget($resp->getMessage());

        return $resp->getMessage(). '1q2w3e';
    }
}