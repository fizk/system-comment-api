<?php

use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Comment\Handler\SetResource;
use Comment\Model;
use Comment\Service\Resource;

class SetResourceTest extends TestCase {
    public function testMessageSavedSuccess() {
        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '')
            ->withAttribute('resource_id', 'resource-id')
            ->withParsedBody([
                'user_id' => 'user-id',
                'name' => 'some name'
            ]);

        $model = (new Model\Resource())
            ->setId('resource-id')
            ->setName('some name')
            ->setUserId('user-id')
            ->setCreated(new DateTime());

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Resource::class);
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
        $response = (new SetResource())
            ->setResourceService($mock)
            ->handle($request);

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testMessageSavedFailure() {
        $request = (new ServerRequestFactory())
            ->createServerRequest('PUT', '')
            ->withAttribute('resource_id', 'resource-id')
            ->withParsedBody([
                'user_id' => 'user-id',
                'name' => 'some name'
            ]);

        $model = (new Model\Resource())
            ->setId('resource-id')
            ->setName('some name')
            ->setUserId('user-id')
            ->setCreated(new DateTime());

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Resource::class);
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
        $response = (new SetResource())
            ->setResourceService($mock)
            ->handle($request);

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }
}
