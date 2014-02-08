<?php

use Trucker\Facades\ResultOrderFactory;

class QueryResultOrderFactoryTest extends TruckerTests
{
    public function testCreateValidResultOrderDriver()
    {
        $this->swapConfig([
            'trucker::search.collection_result_order_driver' => 'get_param'
        ]);
        ResultOrderFactory::setApp($this->app);

        $cond = ResultOrderFactory::build();
        $this->assertTrue(
            ($cond instanceof \Trucker\Finders\Conditions\QueryResultOrderInterface),
            "Expected transporter to implement \Trucker\Finders\Conditions\QueryResultOrderInterface"
        );

        $this->assertTrue(
            ($cond instanceof \Trucker\Finders\Conditions\GetArrayParamsResultOrder),
            "Expected transporter to be \Trucker\Finders\Conditions\GetArrayParamsResultOrder"
        );
    }

    public function testCreateInvalidResultOrderDriver()
    {
        $this->swapConfig([
            'trucker::search.collection_result_order_driver' => 'invalid'
        ]);
        ResultOrderFactory::setApp($this->app);

        $this->setExpectedException('InvalidArgumentException');
        $foo = ResultOrderFactory::build();
    }
}
