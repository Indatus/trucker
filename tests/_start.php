<?php
include __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Trucker\TruckerServiceProvider;
use Trucker\Facades\Config;

abstract class TruckerTests extends PHPUnit_Framework_TestCase
{

    /**
     * The IoC Container
     *
     * @var Container
     */
    protected $app;


    /**
     * Set up the tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->app = new Container;

        // Laravel classes --------------------------------------------- /

        $this->app->instance('path.base', '/src');
        $this->app->instance('path', '/src/app');
        $this->app->instance('path.public', '/src/public');
        $this->app->instance('path.storage', '/src/app/storage');

        $this->app['files']   = new Filesystem;
        $this->app['config']  = $this->getConfig();

        // Trucker classes ------------------------------------------- /

        $serviceProvider = new TruckerServiceProvider($this->app);
        $this->app = $serviceProvider->bindClasses($this->app);

    }

    /**
     * Tears down the tests
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }


    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// DEPENDENCIES /////////////////////////
    ////////////////////////////////////////////////////////////////////


    /**
     * Mock the Config component
     *
     * @return Mockery
     */
    protected function getConfig($options = array())
    {
        $config = Mockery::mock('Illuminate\Config\Repository');
        $config->shouldIgnoreMissing();

        $defaultConfig = [
            'cache.driver'                                               => 'file',
            'database.default'                                           => 'mysql',
            'session.driver'                                             => 'file',
            'trucker::request.base_uri'                                  => 'http://example.com',
            'trucker::request.driver'                                    => 'rest',
            'trucker::response.driver'                                   => 'http_status_code',
            'trucker::error_handler.driver'                              => 'array_response',
            'trucker::auth.driver'                                       => null,
            'trucker::auth.basic.username'                               => null,
            'trucker::auth.basic.password'                               => null,
            'trucker::request.http_method_param'                         => null,
            'trucker::resource.scratch_disk_location'                    => '/tmp',
            'trucker::resource.identity_property'                        => 'id',
            'trucker::transporter.driver'                                => 'json',
            'trucker::resource.collection_key'                           => null,
            'trucker::error_handler.errors_key'                          => 'errors',
            'trucker::query_condition.driver'                            => 'get_array_params',
            'trucker::result_order.driver'                               => 'get_params',
            'trucker::query_condition.get_array_params.container_parameter'               => 'search',
            'trucker::query_condition.get_array_params.property'                          => 'property',
            'trucker::query_condition.get_array_params.operator'                          => 'operator',
            'trucker::query_condition.get_array_params.value'                             => 'value',
            'trucker::query_condition.get_array_params.logical_operator'                  => 'logical_operator',
            'trucker::result_order.get_params.order_by'                          => 'order_by',
            'trucker::result_order.get_params.order_dir'                         => 'order_dir',
            'trucker::query_condition.get_array_params.and_operator'                      => 'AND',
            'trucker::query_condition.get_array_params.or_operator'                       => 'OR',
            'trucker::result_order.get_params.order_dir_ascending'                  => 'ASC',
            'trucker::result_order.get_params.order_dir_descending'                 => 'DESC',
            'trucker::response.http_status.success'                      => 200,
            'trucker::response.http_status.not_found'                    => 404,
            'trucker::response.http_status.invalid'                      => 422,
            'trucker::response.http_status.error'                        => 500,
            'trucker::resource.base_64_property_indication'              => '_base64',
        ];

        foreach ($defaultConfig as $key => $value) {

            $config->set($key, $value);

            if (!array_key_exists($key, $options)) {
                $config->shouldReceive('get')->with($key)->andReturn($value);
            }
        }

        foreach ($options as $key => $value) {
            $config->shouldReceive('get')->with($key)->andReturn($value);
        }

        return $config;
    }

    /**
     * Swap the current config
     *
     * @param  array $config
     *
     * @return void
     */
    protected function swapConfig($config)
    {
        $this->app['config'] = $this->getConfig($config);
        Config::setApp($this->app);
    }

    /**
     * Mock Artisan
     *
     * @return Mockery
     */
    protected function getArtisan()
    {
        $artisan = Mockery::mock('Artisan');
        $artisan->shouldReceive('add')->andReturnUsing(function ($command) {
            return $command;
        });

        return $artisan;
    }


    /**
     * Test helper function that allows you to simulate
     * that a private or protected property was set within a class
     *         
     * @param  Object $class    object to operate on
     * @param  string $property the property to set
     * @param  mixed  $value    the value to set
     * @return void
     */
    protected function simulateSetInaccessableProperty($class, $property, $value)
    {
        $c = new ReflectionClass($class);
        $prop = $c->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($class, $value);
    }


    /**
     * Test helper function that allows you to simulate that a private
     * or protected function was called on a class
     * 
     * @param  Object $class  instance of an object to work with
     * @param  string $method the method to call
     * @param  mixed  $value  the arguments to pass to the method
     * @return void
     */
    protected function invokeInaccessibleMethod($class, $method, $value = null)
    {
        $c = new ReflectionClass($class);
        $meth = $c->getMethod($method);
        $meth->setAccessible(true);
        if ($value) {
            $meth->invoke($class);
        } else {
            $meth->invoke($class, $value);
        }
    }


    /**
     * Determine if two associative arrays are similar
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering 
     * 
     * @param array $a
     * @param array $b
     * @return bool
     */
    public function arraysAreSimilar ($a, $b)
    {
        // if the indexes don't match, return immediately
        if (count(array_diff_assoc($a, $b))) {
            return false;
        }
        // we know that the indexes, but maybe not values, match.
        // compare the values between the two arrays
        foreach ($a as $k => $v) {
            if ($v !== $b[$k]) {
                return false;
            }
        }
        // we have identical indexes, and no unequal values
        return true;
    }
}
