<?php

namespace Imanghafoori\Middlewarize;

trait Middlewarable
{
    public function middleware($middleware)
    {
        return new Proxy($this, $middleware);
    }

    static function middlewared($middlwares)
    {
        return new Proxy(self::class, $middlwares);
    }
}