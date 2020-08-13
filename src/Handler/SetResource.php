<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Comment\Service\{ResourceAware, Resource};
use Comment\Model;
use DateTime;

class SetResource implements RequestHandlerInterface, ResourceAware
{
    private Resource $resourceService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $resource = (new Model\Resource())
            ->setId($request->getAttribute('resource_id'))
            ->setName($request->getParsedBody()['name'])
            ->setUserId($request->getParsedBody()['user_id'])
            ->setCreated(new DateTime());

        return $this->resourceService->save($resource)
            ? new EmptyResponse(201)
            : new EmptyResponse(400)
        ;
    }

    public function setResourceService(Resource $service): self
    {
        $this->resourceService = $service;
        return $this;
    }
}
