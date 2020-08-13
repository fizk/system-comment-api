<?php

use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Comment\Handler\SetMessage;
use Comment\Model;
use Comment\Service\Message;

class SetMessageTest extends TestCase {
    public function testMessageSavedSuccess() {
        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '')
            ->withAttribute('message_id', 'message-id')
            ->withAttribute('resource_id', 'resource-id')
            ->withParsedBody([
                'user_id' => 'user-id',
                'message' => 'some random message'
            ]);

        $model = (new Model\Message())
            ->setId('message-id')
            ->setResourceId('resource-id')
            ->setMessage('some random message')
            ->setUserId('user-id')
            ->setCreated(new DateTime());

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Message::class);
        $mock->shouldReceive('save')
            ->andReturnUsing(function ($arg) use ($model) {
                $this->assertEquals(
                    $model->jsonSerialize(),
                    $arg->jsonSerialize()
                );
                return true;
            })
            ->mock();

        /** @var $response JsonResponse */
        $response = (new SetMessage())
            ->setMessageService($mock)
            ->handle($request);

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testMessageSavedFailure() {
        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '')
            ->withAttribute('message_id', 'message-id')
            ->withAttribute('resource_id', 'resource-id')
            ->withParsedBody([
                'user_id' => 'user-id',
                'message' => 'some random message'
            ]);

        $model = (new Model\Message())
            ->setId('message-id')
            ->setResourceId('resource-id')
            ->setMessage('some random message')
            ->setUserId('user-id')
            ->setCreated(new DateTime());

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Message::class);
        $mock->shouldReceive('save')
            ->andReturnUsing(function ($arg) use ($model) {
                $this->assertEquals(
                    $model->jsonSerialize(),
                    $arg->jsonSerialize()
                );
                return false;
            })
            ->mock();

        /** @var $response JsonResponse */
        $response = (new SetMessage())
            ->setMessageService($mock)
            ->handle($request);

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }
}