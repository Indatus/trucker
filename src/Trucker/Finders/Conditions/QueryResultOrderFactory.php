<?php

namespace Trucker\Finders\Conditions;

use Illuminate\Container\Container;

class QueryResultOrderFactory
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new QueryResultOrderFactory
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
     * Create a result order instance based on configuration
     * 
     * @return \Trucker\finders\Conditions\QueryResultOrderInterface
     */
    public function build()
    {
        $driver = $this->app['config']->get('trucker::search.collection_result_order_driver');

        switch ($driver)
        {
            case 'get_param':
                return new GetArrayParamsResultOrder($this->app);
        }

        throw new \InvalidArgumentException("Unsupported query condition driver [{$driver}]");

    }
}
