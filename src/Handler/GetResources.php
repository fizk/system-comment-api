<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Comment\Service\{ResourceAware, Resource};

class GetResources implements RequestHandlerInterface, ResourceAware
{
    private Resource $resourceService;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $resource = $this->resourceService->fetch();
        return new JsonResponse($resource);
    }

    public function setResourceService(Resource $service): self
    {
        $this->resourceService = $service;
        return $this;
    }
}
