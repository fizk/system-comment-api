<?php

use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\{JsonResponse, EmptyResponse};
use Laminas\Diactoros\ServerRequestFactory;
use Comment\Handler\GetResource;
use Comment\Model;
use Comment\Service\Resource;

class GetResourceTest extends TestCase {
    public function testResourceNotFound() {

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '')
            ->withAttribute('resource_id', 'resource-id');

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Resource::class);
        $mock->shouldReceive('get')
            ->andReturn(null)
            ->mock();

        /** @var $response JsonResponse */
        $response = (new GetResource())
            ->setResourceService($mock)
            ->handle($request);

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetResource(){
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '')
            ->withAttribute('resource_id', 'resource-id');

        $model = (new Model\Resource)
            ->setId('resource-id')
            ->setName('some-name')
            ->setUserId('user-id')
            ->setCreated(new DateTime());
        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Resource::class);
        $mock->shouldReceive('get')
            ->andReturn($model)
            ->mock();

        /** @var $response JsonResponse */
        $response = (new GetResource())
            ->setResourceService($mock)
            ->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($model, $response->getPayload());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
