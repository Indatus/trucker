<?php

namespace Trucker\Transporters;

use Illuminate\Container\Container;

class ApiTransporterFactory
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new ApiTransporterFactory
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
     * Create a transporter instance based on configuration
     * 
     * @return \Trucker\Transporters\TransportableInterface
     */
    public function build()
    {
        $transport = $this->app['config']->get('trucker::transporter');
        
        switch ($transport)
        {
            case 'json':
                return new JsonTransporter;
        }

        throw new \InvalidArgumentException("Unsupported transporter [{$transport}]");
    }
}
