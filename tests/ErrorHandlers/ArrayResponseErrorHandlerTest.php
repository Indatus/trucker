<?php

use Mockery as m;

use Trucker\Responses\ErrorHandlers\ArrayResponseErrorHandler;

class ArrayResponseErrorHandlerTest extends TruckerTests
{

    public function testParseErrors()
    {
        $response = m::mock('Trucker\Responses\Response');
        $response->shouldReceive('parseResponseStringToObject')
            ->once()
            ->andReturn(['name is required', 'address is required']);


        $this->swapConfig([
            'trucker::error_handler_driver'   => 'array_response'
        ]);
        $handler = new ArrayResponseErrorHandler($this->app);

        $errors = $handler->parseErrors($response);

        $this->assertCount(2, $errors, 'Expected 2 errors');
    }
}
