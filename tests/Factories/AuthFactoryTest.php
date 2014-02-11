<?php

use Trucker\Facades\AuthFactory;
use Trucker\Facades\Config;

class AuthFactoryTest extends TruckerTests
{


    public function testCreateValidAuthenticator()
    {
        $this->swapConfig([
            'trucker::auth.driver' => 'basic'
        ]);
        Config::setApp($this->app);

        $json = AuthFactory::build();
        $this->assertTrue(
            ($json instanceof \Trucker\Requests\Auth\BasicAuthenticator),
            "Expected transporter to be Trucker\Requests\Auth\BasicAuthenticator"
        );

        $this->assertTrue(
            ($json instanceof \Trucker\Requests\Auth\AuthenticationInterface),
            "Expected transporter to implement Trucker\Requests\Auth\AuthenticationInterface"
        );
    }

    public function testCreateInvalidTransporter()
    {
        $this->swapConfig([
            'trucker::auth.driver' => 'invalid'
        ]);
        Config::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = AuthFactory::build();
    }
}
