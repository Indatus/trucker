<?php

namespace Trucker\Finders\Conditions;

use Illuminate\Container\Container;

interface QueryConditionInterface
{

    public function __construct(Container $app);

    public function newInstance();

    public function addCondition($property, $operator, $value);

    public function setLogicalOperator($operator);

    public function getLogicalOperatorAnd();

    public function getLogicalOperatorOr();

    public function addToRequest(&$request);
}
