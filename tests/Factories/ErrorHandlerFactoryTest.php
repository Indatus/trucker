<?php

use Trucker\Facades\ErrorHandlerFactory;

class ErrorHandlerFactoryTest extends TruckerTests
{

    public function tearDown()
    {
        parent::tearDown();
        $this->swapConfig([]);
        ErrorHandlerFactory::setApp($this->app);
    }

    public function testCreateValidErrorHandler()
    {
        $this->swapConfig([
            'trucker::error_handler_driver' => 'array_response'
        ]);
        ErrorHandlerFactory::setApp($this->app);

        $json = ErrorHandlerFactory::build();
        $this->assertTrue(
            ($json instanceof \Trucker\Responses\ErrorHandlers\ArrayResponseErrorHandler),
            "Expected transporter to be Trucker\Responses\ErrorHandlers\ArrayResponseErrorHandler"
        );

        $this->assertTrue(
            ($json instanceof \Trucker\Responses\ErrorHandlers\ErrorHandlerInterface),
            "Expected transporter to implement Trucker\Responses\ErrorHandlers\ErrorHandlerInterface"
        );
    }

    public function testCreateInvalidTransporter()
    {
        $this->swapConfig([
            'trucker::error_handler_driver' => 'invalid'
        ]);
        ErrorHandlerFactory::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = ErrorHandlerFactory::build();
    }
}
