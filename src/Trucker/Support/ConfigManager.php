<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Support;

use Illuminate\Container\Container;

class ConfigManager
{
    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;

    /**
     * Build a new ConfigManager
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
     * Get an option from the config file
     *
     * @param  string $option
     *
     * @return mixed
     */
    public function get($option)
    {
        return $this->app['config']->get('trucker.' . $option);
    }

    /**
     * Set an option to the config file
     *
     * @param  string $option
     * @param  mixed $value
     *
     * @return mixed
     */
    public function set($option, $value)
    {
        return $this->app['config']->set('trucker.' . $option, $value);
    }

    /**
     * Determine if a config option contains a specific
     *
     * @param  string $option Config value must be an array
     * @param  mixed $value
     *
     * @return bool
     */
    public function contains($option, $value)
    {
        $option = $this->get($option);
        return is_array($option) && in_array($value, $option);
    }
}
