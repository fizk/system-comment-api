<?php

namespace Comment\Service;

use MongoDB\Database;
use MongoDB\BSON\UTCDateTime;
use Psr\EventDispatcher\EventDispatcherInterface;
use Comment\Service\DatabaseAware;
use Comment\Event\{ServiceError, EventDispatcherAware};
use Comment\Model;

class Message implements DatabaseAware, EventDispatcherAware
{
    private Database $client;
    private ?EventDispatcherInterface $eventDispatch;

    public function get(string $id): ?Model\Message
    {
        $document = $this->client->selectCollection('message')->findOne(
            ['_id' => $id],
        );

        return $document
            ? (new Model\Message())
                ->setId($document->getArrayCopy()['_id'])
                ->setResourceId($document->getArrayCopy()['resource_id'])
                ->setUserId($document->getArrayCopy()['user_id'])
                ->setMessage($document->getArrayCopy()['message'])
                ->setCreated($document->getArrayCopy()['created']->toDateTime())
            : null;
    }

    public function save(Model\Message $model): bool
    {
        try {
            $result = $this->client
                ->selectCollection('message')
                ->insertOne(
                    array_merge(
                        $model->jsonSerialize(),
                        ['created' => new UTCDateTime()]
                    )
                );
            return $result->isAcknowledged();
        } catch (\Throwable $e) {
            $this->getEventDispatcher()->dispatch(new ServiceError($e, __METHOD__));
            return false;
        }
    }

    public function fetchByResource(string $resourceId): ?array
    {
        $documents = $this->client->selectCollection('message')->find(
            ['resource_id' => $resourceId],
            ['sort' => ['created' => -1]]
        );

        return $documents
            ?  array_map(function ($document) {
                return (new Model\Message())
                    ->setId($document->getArrayCopy()['_id'])
                    ->setResourceId($document->getArrayCopy()['resource_id'])
                    ->setUserId($document->getArrayCopy()['user_id'])
                    ->setMessage($document->getArrayCopy()['message'])
                    ->setCreated($document->getArrayCopy()['created']->toDateTime());
            }, $documents->toArray())
            : [];
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
