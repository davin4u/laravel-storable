<?php

namespace LaravelStorable\Drivers\MongoDB\Contracts;

/**
 * Interface MongoDBClient
 * @package LaravelStorable\Drivers\MongoDB\Contracts
 */
interface MongoDBClient
{
    /**
     * @param string $name
     * @return MongoDBCollection
     */
    public function collection(string $name) : MongoDBCollection;
}
