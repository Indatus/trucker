<?php

use Mockery as m;

use Trucker\Responses\ErrorHandlers\ParameterKeyErrorHandler;

class ParameterKeyErrorHandlerTest extends TruckerTests
{

    public function testParseErrors()
    {
        $errorsObject = ((object) ['errors' => ['name is required', 'address is required']]);
        $response = m::mock('Trucker\Responses\Response');
        $response->shouldReceive('parseResponseStringToObject')
            ->once()
            ->andReturn($errorsObject);


        $this->swapConfig([
            'trucker::error_handler.driver'   => 'parameter_key',
            'trucker::error_handler.errors_key' => 'errors'
        ]);
        $handler = new ParameterKeyErrorHandler($this->app);

        $errors = $handler->parseErrors($response);
        $this->assertCount(2, $errors, 'Expected 2 errors');

        $errorsObject = ((object) ['issues' => ['name is required', 'address is required']]);
        $response = m::mock('Trucker\Responses\Response');
        $response->shouldReceive('parseResponseStringToObject')
            ->once()
            ->andReturn($errorsObject);

        $this->setExpectedException('InvalidArgumentException');
        $errors = $handler->parseErrors($response);
    }
}
