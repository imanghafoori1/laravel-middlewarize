<?php

namespace Imanghafoori\Middlewarize;

class Middleware
{
    private $object;

    /**
     * Middleware constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    function middleware(...$middlwares)
    {
        return new Proxy($this->object, $middlwares);
    }
}