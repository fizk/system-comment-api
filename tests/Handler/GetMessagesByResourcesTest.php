<?php

use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Comment\Handler\GetMessagesByResource;
use Comment\Service\Message;
use Comment\Model;

class GetMessagesByResourcesTest extends TestCase {
    public function testTrue() {

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '', [])
            ->withAttribute('resource_id', 'resource-id');

        $model = (new Model\Message)
            ->setId('message-id')
            ->setUserId('user-id')
            ->setResourceId('resource-id')
            ->setMessage('some random message')
            ->setCreated(new DateTime());

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Message::class);
        $mock->shouldReceive('fetchByResource')
            ->andReturn([$model])
            ->mock();

        /** @var $response JsonResponse */
        $response = (new GetMessagesByResource())
            ->setMessageService($mock)
            ->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals([$model], $response->getPayload());
        $this->assertEquals(200, $response->getStatusCode());

    }
}
