<?php

namespace Trucker;

use Illuminate\Container\Container;
use Doctrine\Common\Inflector\Inflector;

class UrlGenerator
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new UrlGenerator
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
}
