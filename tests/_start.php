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

    protected $client;

    protected $history;


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
            'cache.driver'                         => 'file',
            'database.default'                     => 'mysql',
            'session.driver'                       => 'file',
            'trucker::base_uri'                    => null,
            'trucker::http_method_param'           => null,
            'trucker::scratch_disk_location'       => '/tmp',
            'trucker::identity_property'           => 'id',
            'trucker::transporter'                 => 'json',
            'trucker::collection_key'              => null,
            'trucker::errors_key'                  => 'errors',
            'trucker::search.container_parameter'  => 'search',
            'trucker::search.property'             => 'property',
            'trucker::search.operator'             => 'operator',
            'trucker::search.value'                => 'value',
            'trucker::search.logical_operator'     => 'logical_operator',
            'trucker::search.order_by'             => 'order_by',
            'trucker::search.order_dir'            => 'order_dir',
            'trucker::search.and_operator'         => 'AND',
            'trucker::search.or_operator'          => 'OR',
            'trucker::search.order_dir_ascending'  => 'ASC',
            'trucker::search.order_dir_descending' => 'DESC',
            'trucker::http_status.success'         => 200,
            'trucker::http_status.not_found'       => 401,
            'trucker::http_status.invalid'         => 422,
            'trucker::http_status.error'           => 500,
            'trucker::base_64_property_indication' => '_base64',
        ];

        foreach ($defaultConfig as $key => $value) {
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

    public function newHttpClient($historyLimit = 5)
    {
        $this->client = new \Guzzle\Http\Client();

        //record history for this client
        $this->history = new \Guzzle\Plugin\History\HistoryPlugin();
        $this->history->setLimit($historyLimit);
        $this->client->addSubscriber($this->history);

        return $this->client;
    }

    public function mockHttpResponse($http_status = 200, $headers = array(), $body = '')
    {
        $mock = new \Guzzle\Plugin\Mock\MockPlugin();
        $mock->addResponse(
            new \Guzzle\Http\Message\Response(
                $http_status,
                $headers,
                $body
            )
        );
        $this->client->addSubscriber($mock);
    }

    
    public function getHttpClient()
    {
        return $this->client;
    }

    public function getHttpClientHistory()
    {
        return $this->history;
    }
}
