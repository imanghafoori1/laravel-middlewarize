<?php

namespace Imanghafoori\Middlewarize;

class StaticProxy
{
    private $obj;

    private $middlewares;

    /**
     * Proxy constructor.
     *
     * @param $callable
     * @param $middlewares
     */
    public function __construct($callable, $middlewares)
    {
        $this->obj = $callable;
        $this->middlewares = $middlewares;
    }

    public function __call($method, $params)
    {
        $pipeline = new Pipeline(app());

        return $pipeline
            ->via('handle')
            ->send($params)
            ->through($this->middlewares)
            ->then(function ($params) use ($method) {
                return call_user_func_array([$this->obj, $method], $params);
            });
    }
}