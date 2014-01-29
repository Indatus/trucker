<?php

namespace Trucker\Finders\Conditions;

use Illuminate\Container\Container;

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
        $instance = new static($this->app);
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


    public function toArray()
    {
        $order_by  = $this->app['config']->get('trucker::search.order_by');
        $order_dir = $this->app['config']->get('trucker::search.order_dir');

        $params             = [];
        $params[$order_by]  = $this->orderByField;
        $params[$order_dir] = $this->orderDirection;
        
        return $params;
    }


    public function toQueryString()
    {
        return http_build_query($this->toArray());
    }
}
