<?php

namespace Comment\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;

class Index implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'status' => 'ok',
            'endpoints' => [
                '/resources' => [
                    'params' => [],
                    'get' => [
                        'response' => [
                            200 => 'array of resources'
                        ],
                        'request' => []
                    ],

                ],
                '/resources/{resource_id}' => [
                    'params' => [
                        'resource_id' => '@string'
                    ],
                    'get' => [
                        'response' => [
                            200 => 'single resource',
                            404 => 'resource not found',
                        ],
                        'request' => []
                    ],
                    'put' => [
                        'response' => [
                            201 => 'resource created',
                            400 => 'invalid arguments',
                        ],
                        'request' => [
                            'name' => '@string',
                            'user_id' => '@string',
                        ]
                    ],

                ],
                '/resources/{resource_id}/messages' => [
                    'params' => [
                        'resource_id' => '@string'
                    ],
                    'get' => [
                        'response' => [
                            200 => 'array of messages'
                        ],
                        'request' => []
                    ],
                ],
                '/resources/{resource_id}/messages/{message_id}' => [
                    'params' => [
                        'resource_id' => '@string',
                        'message_id' => '@string',
                    ],
                    'get' => [
                        'response' => [
                            200 => 'single message',
                            404 => 'message not found',
                        ],
                        'request' => []
                    ],
                    'put' => [
                        'response' => [
                            201 => 'put single message',
                            401 => 'invalid arguments',
                        ],
                        'request' => [
                            'message' => '@string',
                            'user_id' => '@string',
                        ]
                    ],

                ],
            ]
        ]);
    }
}
