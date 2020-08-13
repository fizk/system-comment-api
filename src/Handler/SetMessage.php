<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use DateTime;
use Comment\Service\{MessageAware, Message};
use Comment\Model;

class SetMessage implements RequestHandlerInterface, MessageAware
{
    private Message $messageService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $message = (new Model\Message())
            ->setId($request->getAttribute('message_id'))
            ->setResourceId($request->getAttribute('resource_id'))
            ->setUserId($request->getParsedBody()['user_id'])
            ->setMessage($request->getParsedBody()['message'])
            ->setCreated(new DateTime());

        return $this->messageService->save($message)
            ? new EmptyResponse(201)
            : new EmptyResponse(400)
        ;
    }

    public function setMessageService(Message $service): self
    {
        $this->messageService = $service;
        return $this;
    }
}
