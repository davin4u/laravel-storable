<?php

namespace LaravelStorable\Drivers\MongoDB;

/**
 * Class Mongo
 * @package LaravelStorable\Drivers\MongoDB
 */
class Mongo
{
    /**
     * @var Client
     */
    protected static $client;

    /**
     * @return Client
     */
    public static function client()
    {
        if (is_null(static::$client)) {
            static::$client = new Client();
        }

        return static::$client;
    }
}
