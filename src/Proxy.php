<?php

namespace Imanghafoori\Middlewarize;

class Proxy
{
    private $callable;

    private $middlewares;

    /**
     * Proxy constructor.
     *
     * @param $callable
     * @param $middlewares
     */
    public function __construct($callable, $middlewares)
    {
        $this->callable = $callable;
        $this->middlewares = $middlewares;
    }

    public function __call($method, $params)
    {
        $pipeline = new Pipeline(app());

        if (!is_string($this->callable)) {
            $core = (function ($params) use ($method) {
                try {
                    return $this->$method(...$params);
                } catch (\Throwable $e) {
                    return $e;
                }
            })->bindTo($this->callable, $this->callable);
        } else {
            $core = function ($params) use ($method) {
                return call_user_func_array([$this->callable, $method], $params);
            };
        }

        $result = $pipeline
            ->via('handle')
            ->send($params)
            ->through($this->middlewares)
            ->then($core);

        if ($result instanceof \Throwable) {
            throw $result;
        } else {
            return $result;
        }
    }
}
