<?php

namespace Trucker\Factories;

use Illuminate\Container\Container;
use Trucker\Framework\FactoryDriver;

class QueryConditionFactory extends FactoryDriver
{

    /**
     * Function to return a string representaion of the namespace 
     * that all classes built by the factory should be contained within
     * 
     * @return string - namespace string
     */
    public function getDriverNamespace()
    {
        return "\Trucker\Finders\Conditions";
    }


    /**
     * Function to return a string that should be suffixed
     * to the studly-cased driver name of all the drivers
     * that the factory can return 
     * 
     * @return string
     */
    public function getDriverNameSuffix()
    {
        return "QueryCondition";
    }


    /**
     * Function to return a string that should be prefixed
     * to the studly-cased driver name of all the drivers
     * that the factory can return
     * 
     * @return string
     */
    public function getDriverNamePrefix()
    {
        return "";
    }

    /**
     * Function to return an array of arguments that should be
     * passed to the constructor of a new driver instance
     * 
     * @return array
     */
    public function getDriverArgumentsArray()
    {
        return [$this->app];
    }

    /**
     * Function to return the string representation of the driver 
     * itslef based on a value fetched from the config file.  This
     * function will itself access the config, and return the driver
     * setting
     * 
     * @return string
     */
    public function getDriverConfigValue()
    {
        return $this->app['config']->get('trucker::search.collection_query_condition_driver');
    }
}
