<?php
include __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Trucker\TruckerServiceProvider;

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

        foreach ($options as $key => $value) {
            $config->shouldReceive('get')->with($key)->andReturn($value);
        }

        // Drivers
        $config->shouldReceive('get')->with('cache.driver')->andReturn('file');
        $config->shouldReceive('get')->with('database.default')->andReturn('mysql');
        $config->shouldReceive('get')->with('session.driver')->andReturn('file');

        // Trucker
        $config->shouldReceive('get')->with('trucker::base_uri')->andReturn(null);
        $config->shouldReceive('get')->with('trucker::http_method_param')->andReturn(null);
        $config->shouldReceive('get')->with('trucker::scratch_disk_location')->andReturn('/tmp');
        $config->shouldReceive('get')->with('trucker::identity_property')->andReturn('id');
        $config->shouldReceive('get')->with('trucker::transporter')->andReturn('json');
        $config->shouldReceive('get')->with('trucker::collection_key')->andReturn(null);
        $config->shouldReceive('get')->with('trucker::search.container_parameter')->andReturn('search');
        $config->shouldReceive('get')->with('trucker::search.property')->andReturn('property');
        $config->shouldReceive('get')->with('trucker::search.operator')->andReturn('operator');
        $config->shouldReceive('get')->with('trucker::search.value')->andReturn('value');
        $config->shouldReceive('get')->with('trucker::search.logical_operator')->andReturn('logical_operator');
        $config->shouldReceive('get')->with('trucker::search.order_by')->andReturn('order_by');
        $config->shouldReceive('get')->with('trucker::search.order_dir')->andReturn('order_dir');
        $config->shouldReceive('get')->with('trucker::search.and_operator')->andReturn('AND');
        $config->shouldReceive('get')->with('trucker::search.or_operator')->andReturn('OR');
        $config->shouldReceive('get')->with('trucker::search.order_dir_ascending')->andReturn('ASC');
        $config->shouldReceive('get')->with('trucker::search.order_dir_descending')->andReturn('DESC');

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
        $this->app['trucker.trucker']->disconnect();
        $this->app['config'] = $this->getConfig($config);
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
}
