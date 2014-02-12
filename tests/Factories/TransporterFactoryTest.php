<?php

use Trucker\Facades\TransporterFactory;
use Trucker\Facades\Config;

class TransporterFactoryTest extends TruckerTests
{

    public function tearDown()
    {
        parent::tearDown();
        $this->swapConfig([]);
        Config::setApp($this->app);
    }

    public function testCreateValidTransporter()
    {
        $this->swapConfig([
            'trucker::transporter.driver' => 'json'
        ]);
        Config::setApp($this->app);

        $json = TransporterFactory::build();
        $this->assertTrue(
            ($json instanceof \Trucker\Transporters\JsonTransporter),
            "Expected transporter to be Trucker\Transporters\JsonTransporter\n".
            "But it was ". get_class($json)
        );

        $this->assertTrue(
            ($json instanceof \Trucker\Transporters\TransporterInterface),
            "Expected transporter to implement Trucker\Transporters\TransporterInterface"
        );
    }

    public function testCreateInvalidTransporter()
    {
        $this->swapConfig([
            'trucker::transporter.driver' => 'invalid'
        ]);
        Config::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = TransporterFactory::build();
    }
}
