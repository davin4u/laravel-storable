<?php

namespace LaravelStorable\Drivers\MongoDB;

use LaravelStorable\Drivers\MongoDB\Contracts\MongoDBCollection;

/**
 * Class Collection
 * @package LaravelStorable\Drivers\MongoDB
 */
class Collection implements MongoDBCollection
{
    /**
     * @var \MongoDB\Collection
     */
    protected $collection;

    /**
     * Collection constructor.
     * @param \MongoDB\Collection $collection
     */
    public function __construct(\MongoDB\Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @param array $attributes
     * @return \LaravelStorable\Contracts\Document|null
     */
    public function create(array $attributes) : ?\LaravelStorable\Contracts\Document
    {
        $result = $this->collection->insertOne($attributes);

        if ($result instanceof \MongoDB\InsertOneResult) {
            /** @var \MongoDB\BSON\ObjectId $id */
            $id = $result->getInsertedId();

            /** @var \MongoDB\Model\BSONDocument $doc */
            $doc = $this->collection->findOne(['_id' => $id]);

            if (!is_null($doc)) {
                return new Document($doc, $this->collection->getCollectionName());
            }
        }

        return null;
    }

    /**
     * @param $id
     * @return \LaravelStorable\Contracts\Document|null
     */
    public function find($id) : ?\LaravelStorable\Contracts\Document
    {
        /** @var \MongoDB\Model\BSONDocument $doc */
        $doc = $this->collection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        if (!is_null($doc)) {
            return new Document($doc, $this->collection->getCollectionName());
        }

        return null;
    }

    /**
     * @param array $filter
     * @param array $options
     * @return array
     */
    public function where($filter = [], array $options = []) : array
    {
        $docs = $this->collection->find($filter, $options)->toArray();

        if (!empty($docs)) {
            $collectionName = $this->collection->getCollectionName();

            $docs = array_map(function ($doc) use ($collectionName) {
                return new Document($doc, $collectionName);
            }, $docs);
        }

        return $docs;
    }

    /**
     * @param $id
     * @param array $attributes
     * @return \LaravelStorable\Contracts\Document|null
     */
    public function update($id, array $attributes): ?\LaravelStorable\Contracts\Document
    {
        /** @var \MongoDB\UpdateResult $result */
        $result = $this->collection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($id)],
            ['$set' => $attributes]
        );

        if ($result->getMatchedCount() <= 0) {
            throw new \RuntimeException("Nothing found for given criteria.");
        }

        return $this->find($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id): bool
    {
        /** @var \MongoDB\DeleteResult $result */
        $result = $this->collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        return $result->getDeletedCount() > 0;
    }
}
