<?php

namespace Trucker\Finders\Conditions;

interface QueryResultOrderInterface
{

    public function __construct(Container $app);

    public function newInstance();

    public function setOrderByField($propertyName);

    public function setOrderDirection($direction);

    public function getOrderDirectionAscending();

    public function getOrderDirectionDescending();

    public function addToRequest(&$request);
}
