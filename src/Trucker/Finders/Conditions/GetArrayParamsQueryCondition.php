<?php

namespace Trucker\Finders\Conditions;

use Illuminate\Container\Container;

class GetArrayParamsQueryCondition implements QueryConditionInterface
{

    const PROPERTY = 'property';

    const OPERATOR = 'operator';

    const VALUE = 'value';

    protected $app;

    protected $conditions = [];

    protected $logicalOperator;


    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    public function newInstance()
    {
        $instance = new static($this->app);
        return $instance;
    }


    public function addCondition($property, $operator, $value)
    {
        $this->conditions[] = [
            self::PROPERTY => $property,
            self::OPERATOR => $operator,
            self::VALUE    => $value
        ];
    }


    public function setLogicalOperator($operator)
    {
        if ($operator != $this->getLogicalOperatorAnd() &&
            $operator != $this->getLogicalOperatorOr()
        ) {
            throw new Exception("Invalid logical operator: {$operator}");
        }

        $this->logicalOperator = $operator;
    }


    public function getLogicalOperatorAnd()
    {
        return $this->app['config']->get('trucker::search.and_operator');
    }


    public function getLogicalOperatorOr()
    {
        return $this->app['config']->get('trucker::search.or_operator');
    }


    public function addToRequest(&$request)
    {
        $query     = $request->getQuery();
        
        $conatiner = $this->app['config']->get('trucker::search.container_parameter');
        $property  = $this->app['config']->get('trucker::search.property');
        $operator  = $this->app['config']->get('trucker::search.operator');
        $value     = $this->app['config']->get('trucker::search.value');

        $x = 0;
        foreach ($this->conditions as $condition) {

            $query->add(
                "{$conatiner}[$x][{$property}]",
                $condition[self::PROPERTY]
            );
            $query->add(
                "{$conatiner}[$x][{$operator}]",
                $condition[self::OPERATOR]
            );
            $query->add(
                "{$conatiner}[$x][{$value}]",
                $condition[self::VALUE]
            );

            $x++;

        }//end foreach $findConditions

        if (isset($this->logicalOperator)) {
            $query->add(
                $this->app['config']->get('trucker::search.logical_operator'),
                $this->logicalOperator
            );
        }
    }


    public function toArray()
    {

        $conatiner = $this->app['config']->get('trucker::search.container_parameter');
        $property  = $this->app['config']->get('trucker::search.property');
        $operator  = $this->app['config']->get('trucker::search.operator');
        $value     = $this->app['config']->get('trucker::search.value');

        $params = [];

        $x = 0;
        foreach ($this->conditions as $condition) {

            $params["{$conatiner}[$x][{$property}]"] = $condition[self::PROPERTY];
            $params["{$conatiner}[$x][{$operator}]"] = $condition[self::OPERATOR];
            $params["{$conatiner}[$x][{$value}]"] = $condition[self::VALUE];

            $x++;

        }//end foreach $findConditions

        if (isset($this->logicalOperator)) {
            $params[$this->app['config']->get('trucker::search.logical_operator')] = $this->logicalOperator;
        }

        return $params;
    }


    public function toQueryString()
    {
        return http_build_query($this->toArray());
    }
}
