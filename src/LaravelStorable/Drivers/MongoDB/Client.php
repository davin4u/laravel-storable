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
     * @var Client
     */
    protected static $instance;

    /**
     * @var \MongoDB\Client
     */
    protected $client;

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
        $user       = config('laravel-storable.drivers.mongodb.user');
        $password   = config('laravel-storable.drivers.mongodb.password');
        $host       = config('laravel-storable.drivers.mongodb.host');
        $port       = config('laravel-storable.drivers.mongodb.port');
        $db         = config('laravel-storable.drivers.mongodb.database');

        try {
            $this->client = new \MongoDB\Client("mongodb://$user:$password@$host:$port/$db");
        }
        catch (\Exception $e) {
            $this->client = null;
        }

        return $this->client;
    }

    /**
     * @param $name
     * @return Collection
     */
    public function collection(string $name) : MongoDBCollection
    {
        if (is_null($this->client)) {
            $this->createClient();
        }

        return new Collection($this->client->selectCollection(config('laravel-storable.drivers.mongodb.database'), $name));
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (is_null(static::$instance)) {
            static::$instance = (new static())->createClient();
        }

        if (method_exists(static::$instance, $name)) {
            return $arguments ? static::$instance->{$name}($arguments) : static::$instance->{$name}();
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
        if (is_null(static::$instance)) {
            static::$instance = (new static())->createClient();
        }

        if (method_exists(static::$instance, $name)) {
            return $arguments ? static::$instance->{$name}($arguments) : static::$instance->{$name}();
        }

        throw new \RuntimeException("Method $name does not exist.");
    }

    /**
     * @return mixed
     */
    final public function __clone()
    {
        if (is_null(static::$instance)) {
            static::$instance = (new static())->createClient();
        }

        return static::$instance;
    }
}
