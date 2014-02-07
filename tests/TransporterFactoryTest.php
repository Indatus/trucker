<?php

use Trucker\Transporters\TransporterFactory;

class TransporterFactoryTest extends TruckerTests
{
    public function testCreateValidTransporter()
    {
        $json = TransporterFactory::build('json');
        $this->assertTrue(
            ($json instanceof \Trucker\Transporters\JsonTransporter),
            "Expected transporter to be Trucker\Transporters\JsonTransporter"
        );

        $this->assertTrue(
            ($json instanceof \Trucker\Transporters\TransporterInterface),
            "Expected transporter to implement Trucker\Transporters\TransporterInterface"
        );
    }

    public function testCreateInvalidTransporter()
    {
        $this->setExpectedException('InvalidArgumentException');
        $foo = TransporterFactory::build("invalid-transporter-name");
    }
}
