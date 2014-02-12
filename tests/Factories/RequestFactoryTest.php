<?php

use Trucker\Facades\RequestFactory;
use Trucker\Facades\Config;

class RequestFactoryTest extends TruckerTests
{


    public function testCreateValidRequest()
    {
        $this->swapConfig([
            'trucker::request.driver' => 'rest'
        ]);
        Config::setApp($this->app);

        $request = RequestFactory::build();
        $this->assertTrue(
            ($request instanceof \Trucker\Requests\RestRequest),
            "Expected transporter to be Trucker\Requests\RestRequest"
        );

        $this->assertTrue(
            ($request instanceof \Trucker\Requests\RequestableInterface),
            "Expected transporter to implement Trucker\Requests\RequestableInterface"
        );
    }

    public function testCreateInvalidRequest()
    {
        $this->swapConfig([
            'trucker::request.driver' => 'invalid'
        ]);
        Config::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = RequestFactory::build();
    }
}
