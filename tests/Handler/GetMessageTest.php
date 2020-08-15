<?php

use Comment\Handler\GetMessage;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\{JsonResponse, EmptyResponse};
use Laminas\Diactoros\ServerRequestFactory;
use Comment\Handler\GetResource;
use Comment\Model;
use Comment\Service\Message;

class GetMessageTest extends TestCase {
    public function testResourceNotFound() {

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '')
            ->withAttribute('message_id', 'message-id');

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Message::class);
        $mock->shouldReceive('get')
            ->andReturn(null)
            ->mock();

        /** @var $response JsonResponse */
        $response = (new GetMessage())
            ->setMessageService($mock)
            ->handle($request);

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetResource(){
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '')
            ->withAttribute('message_id', 'message-id');

        $model = (new Model\Message)
            ->setId('message-id')
            ->setMessage('some random message')
            ->setResourceId('resource-id')
            ->setUserId('user-id')
            ->setCreated(new DateTime());
        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Message::class);
        $mock->shouldReceive('get')
            ->andReturn($model)
            ->mock();

        /** @var $response JsonResponse */
        $response = (new GetMessage())
            ->setMessageService($mock)
            ->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($model, $response->getPayload());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
