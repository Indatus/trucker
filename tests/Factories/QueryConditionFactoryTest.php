<?php

use Trucker\Facades\ConditionFactory;
use Trucker\Facades\Config;

class QueryConditionFactoryTest extends TruckerTests
{
    public function testCreateValidQueryConditionDriver()
    {
        $this->swapConfig([
            'trucker::query_condition.driver' => 'get_array_params'
        ]);
        Config::setApp($this->app);

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
            'trucker::query_condition.driver' => 'invalid'
        ]);
        Config::setApp($this->app);

        $this->setExpectedException('ReflectionException');
        $this->setExpectedException('InvalidArgumentException');
        $foo = ConditionFactory::build();
    }
}
