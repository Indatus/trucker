<?php

use Trucker\Facades\ResultOrderFactory;
use Trucker\Facades\Config;

class QueryResultOrderFactoryTest extends TruckerTests
{
    public function testCreateValidResultOrderDriver()
    {
        $this->swapConfig([
            'trucker::result_order.driver' => 'get_params'
        ]);
        Config::setApp($this->app);

        $cond = ResultOrderFactory::build();
        $this->assertTrue(
            ($cond instanceof \Trucker\Finders\Conditions\QueryResultOrderInterface),
            "Expected transporter to implement \Trucker\Finders\Conditions\QueryResultOrderInterface"
        );

        $this->assertTrue(
            ($cond instanceof \Trucker\Finders\Conditions\GetParamsResultOrder),
            "Expected transporter to be \Trucker\Finders\Conditions\GetParamsResultOrder"
        );
    }

    public function testCreateInvalidResultOrderDriver()
    {
        $this->swapConfig([
            'trucker::result_order.driver' => 'invalid'
        ]);
        Config::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = ResultOrderFactory::build();
    }
}
