<?php

namespace Comment\Model;

use JsonSerializable;
use DateTime;

class Message implements JsonSerializable
{
    private string $id;
    private string $resourceId;
    private string $userId;
    private string $message;
    private DateTime $created;

    public function jsonSerialize()
    {
        return [
            '_id' => $this->id,
            'resource_id' => $this->resourceId,
            'user_id' => $this->userId,
            'message' => $this->message,
            'created' => $this->created->format('c'),
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $id): self
    {
        $this->userId = $id;
        return $this;
    }

    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    public function setResourceId(string $id): self
    {
        $this->resourceId = $id;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }
}
