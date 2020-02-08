<?php

namespace LaravelStorable\Contracts;

/**
 * Interface Storage
 * @package LaravelStorable\Contracts
 */
interface Storage
{
    /**
     * @param $id
     * @param $collection
     * @return mixed
     */
    public function find($id, $collection = null);

    /**
     * @param array $filter
     * @param array $options
     * @param $collection
     * @return array
     */
    public function where($filter = [], array $options = [], $collection = null) : array;

    /**
     * @param $id
     * @param $attributes
     * @param $collection
     * @return mixed
     */
    public function update($id, $attributes, $collection = null);

    /**
     * @param $attributes
     * @param $collection
     * @return mixed
     */
    public function create($attributes, $collection = null);

    /**
     * @param $id
     * @param $collection
     * @return mixed
     */
    public function delete($id, $collection = null);

    /**
     * @param string $name
     * @return mixed
     */
    public function createCollection(string $name);

    /**
     * @param string $name
     * @return mixed
     */
    public function dropCollection(string $name);
}
