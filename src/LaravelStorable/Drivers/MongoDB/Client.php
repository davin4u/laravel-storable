<?php

namespace LaravelStorable\Drivers\MongoDB;

use LaravelStorable\Drivers\MongoDB\Contracts\MongoDBClient;
use LaravelStorable\Drivers\MongoDB\Contracts\MongoDBCollection;

/**
 * Class Client
 * @package LaravelStorable\Drivers\MongoDB
 */
class Client implements MongoDBClient
{
    /**
     * @var \MongoDB\Client
     */
    protected static $client = null;

    /**
     * Client constructor.
     */
    final public function __construct()
    {

    }

    /**
     * Create MongoDB client
     */
    public function createClient()
    {
        if (!is_null(static::$client)) {
            return static::$client;
        }

        $user       = config('laravel-storable.drivers.mongodb.user');
        $password   = config('laravel-storable.drivers.mongodb.password');
        $host       = config('laravel-storable.drivers.mongodb.host');
        $port       = config('laravel-storable.drivers.mongodb.port');
        $db         = config('laravel-storable.drivers.mongodb.database');

        try {
            static::$client = new \MongoDB\Client("mongodb://$user:$password@$host:$port/$db");
        }
        catch (\Exception $e) {
            static::$client = null;
        }

        return static::$client;
    }

    /**
     * @param $name
     * @return Collection
     */
    public function collection(string $name) : MongoDBCollection
    {
        if (is_null(static::$client)) {
            $this->createClient();
        }

        return new Collection(static::$client->selectCollection(config('laravel-storable.drivers.mongodb.database'), $name));
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (is_null(static::$client)) {
            static::$client = (new static())->createClient();
        }

        if (method_exists(static::$client, $name)) {
            return $arguments ? static::$client->{$name}(...$arguments) : static::$client->{$name}();
        }

        throw new \RuntimeException("Method $name does not exist.");
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (is_null(static::$client)) {
            static::$client = (new static())->createClient();
        }

        if (method_exists(static::$client, $name)) {
            return $arguments ? static::$client->{$name}(...$arguments) : static::$client->{$name}();
        }

        throw new \RuntimeException("Method $name does not exist.");
    }
}
