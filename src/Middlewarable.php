<?php

namespace Imanghafoori\Middlewarize;

trait Middlewarable
{
    public function middleware($middleware)
    {
        return new Proxy($this, $middleware);
    }
}