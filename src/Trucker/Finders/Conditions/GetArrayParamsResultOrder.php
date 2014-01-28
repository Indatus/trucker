<?php

namespace Trucker\Finders\Conditions;

class GetArrayParamsResultOrder implements QueryResultOrderInterface
{

    protected $app;

    protected $orderByField;

    protected $orderDirection;


    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    public function newInstance()
    {
        $instance = new static;
        $instance->setApp($this->app);
        return $instance;
    }


    public function setOrderByField($propertyName)
    {
        $this->orderByField = $propertyName;
    }


    public function setOrderDirection($direction)
    {

        if ($direction != $this->getOrderDirectionAscending() &&
            $direction != $this->getOrderDirectionDescending()
        ) {
            throw new Exception("Invalid order direction: {$direction}");
        }

        $this->orderDirection = $direction;
    }


    public function getOrderDirectionAscending()
    {
        return $this->app['config']->get('trucker::search.order_dir_ascending');
    }


    public function getOrderDirectionDescending()
    {
        return $this->app['config']->get('trucker::search.order_dir_descending');
    }


    public function addToRequest(&$request)
    {
        $query = $request->getQuery();

        if (isset($this->orderByField)) {
            $query->add(
                $this->app['config']->get('trucker::search.order_by'),
                $this->orderByField
            );
        }

        if (isset($this->orderDirection)) {
            $query->add(
                $this->app['config']->get('trucker::search.order_dir'),
                $this->orderDirection
            );
        }
    }
}
