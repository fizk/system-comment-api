<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Comment\Event\{EntryView, EventDispatcherAware};
use Comment\Service\{MessageAware, Message};

class GetMessagesByResource implements RequestHandlerInterface, MessageAware, EventDispatcherAware
{
    private Message $messageService;
    private ?EventDispatcherInterface $eventDispatch = null;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('resource_id');
        $message = $this->messageService->fetchByResource($id);
        $this->getEventDispatcher()->dispatch(new EntryView($request, 'RESOURCE', $id));
        return new JsonResponse($message);
    }

    public function setMessageService(Message $service): self
    {
        $this->messageService = $service;
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
