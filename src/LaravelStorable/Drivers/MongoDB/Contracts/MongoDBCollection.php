<?php

namespace LaravelStorable\Drivers\MongoDB\Contracts;

use LaravelStorable\Contracts\Document;

/**
 * Interface MongoDBCollection
 * @package LaravelStorable\Drivers\MongoDB\Contracts
 */
interface MongoDBCollection
{
    /**
     * @param array $attributes
     * @return Document|null
     */
    public function create(array $attributes) : ?Document;

    /**
     * @param $id
     * @return Document|null
     */
    public function find($id) : ?Document;

    /**
     * @param array $filter
     * @param array $options
     * @return array
     */
    public function where($filter = [], array $options = []) : array;

    /**
     * @param $id
     * @param array $attributes
     * @return Document|null
     */
    public function update($id, array $attributes) : ?Document;

    /**
     * @param $id
     * @return bool
     */
    public function delete($id) : bool;
}
