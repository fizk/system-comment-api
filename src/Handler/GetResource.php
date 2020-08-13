<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\{JsonResponse, EmptyResponse};
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Comment\Service\{ResourceAware, Resource};

class GetResource implements RequestHandlerInterface, ResourceAware
{
    private Resource $resourceService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $resource = $this->resourceService->get($request->getAttribute('resource_id'));
        return $resource
            ? new JsonResponse($resource)
            : new EmptyResponse(404);
    }

    public function setResourceService(Resource $service): self
    {
        $this->resourceService = $service;
        return $this;
    }
}
