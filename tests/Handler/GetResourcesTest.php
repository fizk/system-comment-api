<?php

use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Comment\Handler\GetResources;
use Comment\Service\Resource;

class GetResourcesTest extends TestCase {
    public function testTrue() {

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '', []);

        /** @var $mock \Mockery\MockInterface|\Mockery\LegacyMockInterface */
        $mock = \Mockery::mock(Resource::class);
        $mock->shouldReceive('fetch')
            ->andReturn([])
            ->mock();

        /** @var $response JsonResponse */
        $response = (new GetResources())
            ->setResourceService($mock)
            ->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals([], $response->getPayload());
        $this->assertEquals(200, $response->getStatusCode());

    }
}