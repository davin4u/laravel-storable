<?php

namespace LaravelStorable\Traits;

use LaravelStorable\Contracts\Document;
use LaravelStorable\Contracts\Storage;

/**
 * Trait Storable
 * @package LaravelStorable\Traits
 */
trait Storable
{
    /**
     * @var Document
     */
    protected $storable = null;

    /** @var Storage $storage */
    protected static $storage;

    /**
     * @return Document|mixed|null
     */
    public function getStorableDocument()
    {
        if (!is_null($this->storable)) {
            return $this->storable;
        }

        $storableKey = property_exists($this, 'storableKey') ? $this->storableKey : 'storable_id';

        $driver = config('laravel-storable.driver');

        $storableCollection = property_exists($this, 'storableCollection')
                                    ? $this->storableCollection
                                    : config("laravel-storable.drivers.{$driver}.collection");

        if (is_null($this->{$storableKey})) {
            return null;
        }

        if (is_null(static::$storage)) {
            static::$storage = app()->make(Storage::class);
        }

        return $this->storable = static::$storage->find($this->{$storableKey}, $storableCollection);
    }

    /**
     * Save storable document
     */
    public function saveStorableDocument()
    {
        $storableKey = property_exists($this, 'storableKey') ? $this->storableKey : 'storable_id';

        $driver = config('laravel-storable.driver');

        $storableCollection = property_exists($this, 'storableCollection')
                                    ? $this->storableCollection
                                    : config("laravel-storable.drivers.{$driver}.collection");

        if (is_null($this->storable) && is_null($this->getStorableDocument())) {
            if (is_null(static::$storage)) {
                static::$storage = app()->make(Storage::class);
            }

            $this->storable = static::$storage->create($this->getStorableData(), $storableCollection);

            $this->update([
                $storableKey => $this->storable->getDocumentId()
            ]);

            return;
        }

        foreach ($this->getStorableData() as $key => $value) {
            $this->storable->{$key} = $value;
        }

        $this->storable->save();
    }

    /**
     * Delete storable document
     */
    public function deleteStorableDocument()
    {
        $storableKey = property_exists($this, 'storableKey') ? $this->storableKey : 'storable_id';

        if (!is_null($this->{$storableKey})) {
            if (is_null($this->storable) && is_null($this->getStorableDocument())) {
                return;
            }

            $this->storable->delete();

            $this->update([
                $storableKey => null
            ]);
        }
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getStorableDocumentId()
    {
        if (is_null($this->storable) && is_null($this->getStorableDocument())) {
            return null;
        }

        return (string) $this->storable['_id'];
    }

    /**
     * @param $query
     * @param $storableId
     * @return mixed
     */
    public function scopeWhereStorable($query, $storableId)
    {
        $storableKey = property_exists($this, 'storableKey') ? $this->storableKey : 'storable_id';

        return $query->whereIn($storableKey, is_array($storableId) ? $storableId : [$storableId]);
    }

    /**
     * @return mixed
     */
    protected function getStorableData()
    {
        if (method_exists($this, 'toStorableDocument')) {
            return $this->toStorableDocument();
        }

        return $this->toArray();
    }
}
