<?php

use Trucker\Facades\ConditionFactory;
use Illuminate\Support\Facades\Facade;

class QueryConditionFactoryTest extends TruckerTests
{
    public function testCreateValidQueryConditionDriver()
    {
        $this->swapConfig([
            'trucker::search.collection_query_condition_driver' => 'get_array'
        ]);
        ConditionFactory::setApp($this->app);

        $cond = ConditionFactory::build();
        $this->assertTrue(
            ($cond instanceof \Trucker\Finders\Conditions\QueryConditionInterface),
            "Expected transporter to implement \Trucker\Finders\Conditions\QueryConditionInterface"
        );

        $this->assertTrue(
            ($cond instanceof \Trucker\Finders\Conditions\GetArrayParamsQueryCondition),
            "Expected transporter to be \Trucker\Finders\Conditions\GetArrayParamsQueryCondition"
        );
    }


    public function testCreateInvalidQueryConditionDriver()
    {
        $this->swapConfig([
            'trucker::search.collection_query_condition_driver' => 'invalid'
        ]);
        ConditionFactory::setApp($this->app);

        $this->setExpectedException('InvalidArgumentException');
        $foo = ConditionFactory::build();
    }


    public function testAppGetterSetter()
    {
        $app = Mockery::mock('Illuminate\Container\Container');
        ConditionFactory::setApp($app);
        $this->assertEquals(
            $app,
            ConditionFactory::getApp()
        );
    }
}
