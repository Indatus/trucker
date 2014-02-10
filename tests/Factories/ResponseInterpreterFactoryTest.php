<?php

use Trucker\Facades\ResponseInterpreterFactory;

class ResponseInterpreterFactoryTest extends TruckerTests
{

    public function tearDown()
    {
        parent::tearDown();
        $this->swapConfig([]);
        ResponseInterpreterFactory::setApp($this->app);
    }

    public function testCreateValidInterpreter()
    {
        $this->swapConfig([
            'trucker::response_interpreter_driver' => 'http_status_code'
        ]);
        ResponseInterpreterFactory::setApp($this->app);

        $json = ResponseInterpreterFactory::build();
        $this->assertTrue(
            ($json instanceof \Trucker\Responses\Interpreters\HttpStatusCodeInterpreter),
            "Expected transporter to be Trucker\Responses\Interpreters\HttpStatusCodeInterpreter"
        );

        $this->assertTrue(
            ($json instanceof \Trucker\Responses\Interpreters\ResponseInterpreterInterface),
            "Expected transporter to implement Trucker\Responses\Interpreters\ResponseInterpreterInterface"
        );
    }

    public function testCreateInvalidInterpreter()
    {
        $this->swapConfig([
            'trucker::response_interpreter_driver' => 'invalid'
        ]);
        ResponseInterpreterFactory::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = ResponseInterpreterFactory::build();
    }
}
