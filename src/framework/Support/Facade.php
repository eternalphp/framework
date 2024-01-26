<?php


namespace framework\Support;
use framework\Exception\RuntimeException;

abstract class Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    protected static function getFacadeInstance(){
        $accessor = static::getFacadeAccessor();

        if(!isset($accessor['name'])){
            throw new RuntimeException('A facade name has not been set.');
        }

        if(!isset($accessor['class'])){
            throw new RuntimeException('A facade class has not been set.');
        }

        if(!app()->has($accessor['name'])){
            app()->bind($accessor['name'],$accessor['class']);
        }
        return app()->get($accessor['name']);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeInstance();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }
}