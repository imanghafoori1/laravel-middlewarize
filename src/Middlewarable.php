<?php

namespace Imanghafoori\Middlewarize;

trait Middlewarable
{
    /**
     * @param array|string|callable $middleware
     *
     * @return $this
     */
    public function middleware($middleware)
    {
        return new Proxy($this, $middleware);
    }

    public static function middlewared($middlwares)
    {
        return new Proxy(self::class, $middlwares);
    }
}
