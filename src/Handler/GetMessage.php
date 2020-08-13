<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\{JsonResponse, EmptyResponse};
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Comment\Service\{MessageAware, Message};

class GetMessage implements RequestHandlerInterface, MessageAware
{
    private Message $messageService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $message = $this->messageService->get($request->getAttribute('message_id'));
        return $message
            ? new JsonResponse($message)
            : new EmptyResponse(404);
    }

    public function setMessageService(Message $service): self
    {
        $this->messageService = $service;
        return $this;
    }
}
