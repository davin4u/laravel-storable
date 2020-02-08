<?php

namespace LaravelStorable;

use LaravelStorable\Contracts\Storage;
use LaravelStorable\Drivers\MongoDB\Contracts\MongoDBClient;

/**
 * Class MongoDBStorage
 * @package LaravelStorable
 */
class MongoDBStorage implements Storage
{
    /**
     * @var MongoDBClient
     */
    protected $client;

    /**
     * MongoDBProductsStorage constructor.
     * @param MongoDBClient $client
     */
    public function __construct(MongoDBClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param $id
     * @param null $collection
     * @return Contracts\Document|mixed|null
     */
    public function find($id, $collection = null)
    {
        if (is_null($collection)) {
            $collection = config('laravel-storable.drivers.mongodb.collection');
        }

        return $this->client->collection($collection)->find($id);
    }

    /**
     * @param array $filter
     * @param array $options
     * @param null $collection
     * @return array
     */
    public function where($filter = [], array $options = [], $collection = null) : array
    {
        if (is_null($collection)) {
            $collection = config('laravel-storable.drivers.mongodb.collection');
        }

        return $this->client->collection($collection)->where($filter, $options);
    }

    /**
     * @param $id
     * @param $attributes
     * @param null $collection
     * @return Contracts\Document|mixed|null
     */
    public function update($id, $attributes, $collection = null)
    {
        if (is_null($collection)) {
            $collection = config('laravel-storable.drivers.mongodb.collection');
        }

        return $this->client->collection($collection)->update($id, $attributes);
    }

    /**
     * @param $attributes
     * @param null $collection
     * @return Contracts\Document|mixed|null
     */
    public function create($attributes, $collection = null)
    {
        if (is_null($collection)) {
            $collection = config('laravel-storable.drivers.mongodb.collection');
        }

        return $this->client->collection($collection)->create($attributes);
    }

    /**
     * @param $id
     * @param null $collection
     * @return bool|mixed
     */
    public function delete($id, $collection = null)
    {
        if (is_null($collection)) {
            $collection = config('laravel-storable.drivers.mongodb.collection');
        }

        return $this->client->collection($collection)->delete($id);
    }

    /**
     * @param string $name
     * @return void
     */
    public function createCollection(string $name)
    {
        $this->client->selectDatabase(config('laravel-storable.drivers.mongodb.database'))
                     ->createCollection($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public function dropCollection(string $name)
    {
        $this->client->selectDatabase(config('laravel-storable.drivers.mongodb.database'))
                     ->dropCollection($name);
    }
}
