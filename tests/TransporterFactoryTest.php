<?php

use Trucker\Transporters\TransporterFactory;

class TransporterFactoryTest extends TruckerTests
{
    public function testCreateTransporter()
    {
        $json = TransporterFactory::createTransporter('json');
        $this->assertTrue(
            ($json instanceof \Trucker\Transporters\JsonTransporter),
            "Expected transporter to be Trucker\Transporters\JsonTransporter"
        );

        $this->assertTrue(
            ($json instanceof \Trucker\Transporters\TransporterInterface),
            "Expected transporter to implement Trucker\Transporters\TransporterInterface"
        );
    }
}
