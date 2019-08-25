<?php

namespace Imanghafoori\Middlewarize;

use Illuminate\Pipeline\Pipeline as CorePipe;

class Pipeline extends CorePipe
{
    /**
     * Set the array of pipes.
     *
     * @param  callable|array|string  $pipes
     * @return \Illuminate\Pipeline\Pipeline
     */
    public function through($pipes)
    {
        $pipes = is_callable($pipes) ? [$pipes] : $pipes;
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Get a Closure that represents a slice of the application onion.
     *
     * @return \Closure
     */
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    // If the pipe is an instance of a Closure, we will just call it directly but
                    // otherwise we'll resolve the pipes out of the container and call it with
                    // the appropriate method and arguments, returning the results back out.
                    return $pipe($passable, $stack);
                } elseif (! is_object($pipe)) {
                    [$name, $parameters] = $this->parsePipeString($pipe);

                    // If the pipe is a string we will parse the string and resolve the class out
                    // of the dependency injection container. We can then build a callable and
                    // execute the pipe function giving in the parameters that are required.
                    $name = explode('@', $name);
                    $pipe = $this->getContainer()->make($name[0]);

                    $parameters = array_merge([$passable, $stack], $parameters);
                } else {
                    // If the pipe is already an object we'll just make a callable and pass it to
                    // the pipe as-is. There is no need to do any extra parsing and formatting
                    // since the object we're given was already a fully instantiated object.
                    $parameters = [$passable, $stack];
                }

                $method = $name[1] ?? $this->method;

                return method_exists($pipe, $method)
                    ? $pipe->{$method}(...$parameters)
                    : $pipe(...$parameters);
            };
        };
    }
}
