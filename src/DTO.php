<?php

namespace Imanghafoori\Middlewarize;

class DTO
{
    private $middleware;

    private $params;

    /**
     * DTO constructor.
     *
     * @param $middleware
     * @param $params
     */
    public function __construct($middleware, $params)
    {
        $this->middleware = $middleware;
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}