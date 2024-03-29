<?php

namespace Imanghafoori\Middlewarize;

use Illuminate\Container\Container;

trait Middlewarable
{
    /**
     * @param array|string|callable $middleware
     *
     * @return $this
     */
    public function middleware($middleware)
    {
        return self::getStaticProxy($this, $middleware);
    }

    public static function middlewared($middlware)
    {
        return self::getStaticProxy(self::class, $middlware);
    }

    private static function getStaticProxy($class, $middleware)
    {
        return new class ($class, $middleware)
        {
            public function __construct($callable, $middlewares)
            {
                $this->callable = $callable;
                $this->middlewares = $middlewares;
            }

            public function __call($method, $params)
            {
                $pipeline = new Pipeline(Container::getInstance());

                // for static method calls on classes.
                $core = function ($params) use ($method) {
                    try {
                        return call_user_func_array([$this->callable, $method], $params);
                    } catch (\Throwable $e) {
                        return $e;
                    }
                };

                return $pipeline->sendItThroughPipes($params, $core, $this->middlewares);
            }
        };
    }
}
