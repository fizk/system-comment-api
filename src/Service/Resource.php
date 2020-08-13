<?php

namespace Comment\Service;

use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\Exception\ServerException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Comment\Service\DatabaseAware;
use Comment\Event\{ServiceError, EventDispatcherAware};
use Comment\Model;

class Resource implements DatabaseAware, EventDispatcherAware
{
    private Database $client;
    private ?EventDispatcherInterface $eventDispatch;

    public function get(string $id): ?Model\Resource
    {
        $document = $this->client->selectCollection('resource')->findOne([
            '_id' => $id
        ]);
        return $document
            ? (new Model\Resource)
                ->setId($document->getArrayCopy()['_id'])
                ->setName($document->getArrayCopy()['name'])
                ->setUserId($document->getArrayCopy()['user_id'])
                ->setCreated($document->getArrayCopy()['created']->toDateTime())
            : null;
    }

    public function fetch(): array
    {
        $documents = $this->client->selectCollection('resource')->find(
            [],
            ['sort' => ['created' => -1]]
        );

        return $documents
            ?  array_map(function ($document) {
                return (new Model\Resource)
                    ->setId($document->getArrayCopy()['_id'])
                    ->setUserId($document->getArrayCopy()['user_id'])
                    ->setName($document->getArrayCopy()['name'])
                    ->setCreated($document->getArrayCopy()['created']->toDateTime());
            }, $documents->toArray())
            : [];
    }

    public function save(Model\Resource $model): bool
    {
        try {
            $result = $this->client
                ->selectCollection('category')
                ->insertOne(
                    array_merge(
                        $model->jsonSerialize(),
                        ['created' => new UTCDateTime()]
                    )
                );
            return $result->isAcknowledged();
        } catch (ServerException $e) {
            $this->getEventDispatcher()->dispatch(new ServiceError($e, __METHOD__));
            return false;
        }
    }

    public function setDriver(Database $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatch ?: new class implements EventDispatcherInterface {
            public function dispatch(object $event)
            {
            }
        };
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatch): self
    {
        $this->eventDispatch = $eventDispatch;
        return $this;
    }
}
