<?php

namespace Trucker\Finders\Conditions;

use Illuminate\Container\Container;

class QueryConditionFactory
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new QueryConditionFactory
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * Getter to access the IoC Container
     * 
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }


    /**
     * Setter for the IoC Container
     * 
     * @param Container
     * @return  void
     */
    public function setApp($app)
    {
        $this->app = $app;
    }


    /**
     * Create a query condition instance based on configuration
     * 
     * @return \Trucker\finders\Conditions\QueryConditionInterface
     */
    public function build()
    {

        $driver = $this->app['config']->get('trucker::search.collection_query_condition_driver');

        switch ($driver)
        {
            case 'get_array':
                return new GetArrayParamsQueryCondition($this->app);
        }

        throw new \InvalidArgumentException("Unsupported query condition driver [{$driver}]");

    }
}
