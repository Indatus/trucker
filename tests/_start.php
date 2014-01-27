<?php
include __DIR__.'/../vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Trucker\TruckerServiceProvider;
use Trucker\Facades\Request;

abstract class TruckerTests extends PHPUnit_Framework_TestCase
{

    /**
     * The IoC Container
     *
     * @var Container
     */
    protected $app;

    /**
     * History Guzzle Plugin
     * 
     * @var [type]
     */
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
            'trucker::base_uri'                    => 'http://example.com',
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
     * Function to track history for unit testing on a 
     * guzzle client
     * 
     * @param  Guzzle\Client    $client       Guzzle client
     * @param  integer          $historyLimit number of requests to keep
     * @return void
     */
    public function trackHistory(&$client, $historyLimit = 5)
    {
        //record history for this client
        $this->history = new \Guzzle\Plugin\History\HistoryPlugin();
        $this->history->setLimit($historyLimit);
        $client->addSubscriber($this->history);
    }


    /**
     * Function to mock an HTTP response to a Guzzle Request
     * 
     * @param  Guzzle\Client    $client      Guzzle Client to mock response for
     * @param  integer          $http_status HTTP status to return
     * @param  array            $headers     HTTP Headers for the response
     * @param  string           $body        HTTP response body
     * @return void
     */
    public function mockHttpResponse(&$client, $http_status = 200, $headers = array(), $body = '')
    {
        $mock = new \Guzzle\Plugin\Mock\MockPlugin();
        $mock->addResponse(
            new \Guzzle\Http\Message\Response(
                $http_status,
                $headers,
                $body
            )
        );
        $client->addSubscriber($mock);
    }

    /**
     * Function to return the Guzzle History plugin
     * to look at requests for a client
     * 
     * @return Guzzle\Plugin\History
     */
    public function getHttpClientHistory()
    {
        return $this->history;
    }


    /**
     * Helper function to setup for testing requests that leverage
     * the Request class and guzzle
     * 
     * @param  array  $config config overrides
     * @return Guzzle\Http\Client
     */
    protected function &initGuzzleRequestTest($config = array())
    {
        $this->swapConfig($config);
        Request::setApp($this->app);
        $client = Request::getClient();
        $this->trackHistory($client);

        return $client;
    }


    /**
     * Helper function to test various aspects of a Guzzle
     * request / response
     * 
     * @param  string $method      HTTP method expected
     * @param  string $baseUri     Base URL expected in request
     * @param  string $uri         URI expected for request
     * @param  array  $queryParams Assoc array of querystring params expected
     * @param  array  $postParams  Assoc array of post params expected
     * @param  array  $fileParams  Assoc array (name => path) of files expected
     * @return void
     */
    protected function makeGuzzleAssertions(
        $method,
        $baseUri,
        $uri,
        $queryParams = [],
        $postParams = [],
        $fileParams = []
    ) {

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();

        //assert the HTTP REQUEST is manufactured as it should be
        $this->assertEquals(
            last(explode('/', $baseUri)),
            $request->getHost(),
            "The request host is wrong"
        );
        $this->assertEquals(
            $uri,
            $request->getPath(),
            "The request path is wrong"
        );
        $this->assertEquals(
            http_build_query($queryParams),
            $request->getQuery(true),
            "The querystring params are wrong"
        );

        //if request can have post / files
        if (!in_array($method, ['GET', 'HEAD', 'TRACE', 'OPTIONS'])) {

            //test the post parameters
            $this->assertEquals(
                $postParams,
                $request->getPostFields()->getAll(),
                "The post params are wrong"
            );

            //test the files
            $files = $request->getPostFiles();

            $this->assertCount(
                count($fileParams),
                $files,
                "The file upload count is wrong"
            );
            $this->assertEquals(
                array_keys($fileParams),
                array_keys($files),
                "The file upload fields are wrong"
            );

            foreach ($fileParams as $field => $path) {
                $this->assertArrayHasKey(
                    $field,
                    $files,
                    "The file upload fields appear to be missing: {$field}"
                );

                if (array_key_exists($field, $files)) {
                    $postFileObj = $files[$field][0];
                    $this->assertEquals(
                        $path,
                        $post->getFilename(),
                        "The filename is wrong for the '{$field}' file"
                    );
                }
            }

        }//end if request can have post / files

        $this->assertEquals(
            $method,
            $request->getMethod(),
            "The HTTP method is wrong"
        );
        $this->assertEquals(
            $this->app['config']->get('trucker::transporter'),
            last(explode('/', $request->getHeader('Accept'))),
            "The transport language is wrong"
        );
    }
}
