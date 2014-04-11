<?php

use Mockery as m;

use Trucker\Responses\Interpreters\HttpStatusCodeInterpreter;

class HttpStatusCodeInterpreterTest extends TruckerTests
{

    public function testSuccessResponse()
    {
        $response = $this->mockResponse(200);
        $interpreter = $this->getInterpreter();
        $this->assertTrue($interpreter->success($response), 'Response should have been successful');

        $response = $this->mockResponse(200);
        $interpreter = $this->getInterpreter([
                'trucker::response.http_status.success'   => [200, 201]
            ]);
        $this->assertTrue($interpreter->success($response), 'Response should have been successful');
    }

    public function testNotFoundResponse()
    {
        $response = $this->mockResponse(404);
        $interpreter = $this->getInterpreter();
        $this->assertTrue($interpreter->notFound($response), 'Response should have been not found');

        //405 isn't really not found, but we need to test with something
        $response = $this->mockResponse(404);
        $interpreter = $this->getInterpreter([
                'trucker::response.http_status.success'   => [404, 405]
            ]);
        $this->assertTrue($interpreter->success($response), 'Response should have been not found');
    }

    public function testInvalidResponse()
    {
        $response = $this->mockResponse(422);
        $interpreter = $this->getInterpreter();
        $this->assertTrue($interpreter->invalid($response), 'Response should have been invalid');

        //416 isn't really invalid, but we need to test with something
        $response = $this->mockResponse(422);
        $interpreter = $this->getInterpreter([
                'trucker::response.http_status.success'   => [422, 416]
            ]);
        $this->assertTrue($interpreter->success($response), 'Response should have been invalid');
    }

    public function testErrorResponse()
    {
        $response = $this->mockResponse(500);
        $interpreter = $this->getInterpreter();
        $this->assertTrue($interpreter->error($response), 'Response should have been error');

        $response = $this->mockResponse(500);
        $interpreter = $this->getInterpreter([
                'trucker::response.http_status.success'   => [500, 503]
            ]);
        $this->assertTrue($interpreter->success($response), 'Response should have been error');
    }

    private function getInterpreter($overwriteConfig = [])
    {
        $swapWith = array_merge([
            'trucker::response.http_status.success'   => '200',
            'trucker::response.http_status.not_found' => '404',
            'trucker::response.http_status.invalid'   => '422',
            'trucker::response.http_status.error'     => '500',
        ], $overwriteConfig);
        $this->swapConfig($swapWith);
        return new HttpStatusCodeInterpreter($this->app);
    }

    private function mockResponse($statusCode)
    {
        $response = m::mock('Trucker\Responses\Response');
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn($statusCode);
        return $response;
    }
}
