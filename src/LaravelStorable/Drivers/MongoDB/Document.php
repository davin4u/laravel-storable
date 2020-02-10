<?php

namespace LaravelStorable\Drivers\MongoDB;


use LaravelStorable\Contracts\Storage;

/**
 * Class Document
 * @package LaravelStorable\Drivers\MongoDB
 */
class Document implements \LaravelStorable\Contracts\Document
{
    /**
     * @var mixed|array
     */
    protected $doc;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var string|null
     */
    protected $storableCollection = null;

    /**
     * Document constructor.
     * @param \MongoDB\Model\BSONDocument $doc
     * @param null $collection
     */
    public function __construct(\MongoDB\Model\BSONDocument $doc, $collection = null)
    {
        $handler = function (array $properties, callable $handler) {
            foreach ($properties as $key => $prop) {
                if (is_iterable($prop)) {
                    $properties[$key] = $handler((array)$prop, $handler);
                }
            }

            return $properties;
        };

        $this->doc = $handler((array) $doc, $handler);

        $this->storage = app(Storage::class);

        $this->storableCollection = !is_null($collection) ? $collection : config('laravel-storable.drivers.mongodb.collection');

        unset($handler);
    }

    /**
     * Update storable document
     * @param array $attributes
     * @return mixed
     */
    public function update(array $attributes = []) : bool
    {
        if (!is_null($this->doc['_id'])) {
            /** @var Document $updated */
            $updated = $this->storage->update($this->doc['_id'], $attributes);

            if ($updated) {
                $this->doc = $updated->getDoc();

                return true;
            }
        }

        return false;
    }

    /**
     * Save storable document
     */
    public function save()
    {
        $attributes = $this->doc;

        unset($attributes['_id']);

        if (!empty($attributes)) {
            if (!is_null($this->doc['_id'])) {
                $this->update($attributes);
            }
            else {
                /** @var \LaravelStorable\Contracts\Document $created */
                $created = $this->storage->create($attributes);

                $this->doc = $created->getDoc();
            }
        }
    }

    /**
     * Delete storable document
     * @return bool|void
     */
    public function delete()
    {
        if (!is_null($this->doc['_id'])) {
            if ($this->storage->delete($this->doc['_id'])) {
                $this->doc = [];

                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $asArray
     * @return array|mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @return string
     */
    public function getDocumentId() : string
    {
        /** @var \MongoDB\BSON\ObjectId $objectId */
        $objectId = $this->doc['_id'];

        return (string) $objectId;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return isset($this->doc[$name]) ? $this->doc[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->doc[$name] = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->doc[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return isset($this->doc[$offset]) ? $this->doc[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->doc[] = $value;
        }
        else {
            $this->doc[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->doc[$offset]);
    }
}
