<?php

namespace LaravelStorable\Contracts;

/**
 * Interface Document
 * @package LaravelStorable\Contracts
 */
interface Document
{
    /**
     * @param array $attributes
     * @return bool
     */
    public function update(array $attributes) : bool;

    /**
     * @return void
     */
    public function save();

    /**
     * @return void
     */
    public function delete();

    /**
     * @return mixed
     */
    public function getDocumentId() : string;

    /**
     * @return mixed
     */
    public function getDoc();
}
