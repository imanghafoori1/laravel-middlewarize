<?php


namespace Imanghafoori\Middlewarize\Tests\helpers\Middlewares;

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

    public static function handle3($data, $next)
    {
        return $next($data) + 1;
    }
}
