<?php

use Trucker\Facades\Config;
use Trucker\Facades\RequestFactory;

trait GuzzleTestingTrait
{
    /**
     * History Guzzle Plugin
     *
     * @var [type]
     */
    protected $history;

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
        Config::setApp($this->app);
        $client = RequestFactory::getClient();
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
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

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

        $this->assertTrue(
            $this->arraysAreSimilar(
                $queryParams,
                $request->getQuery()->toArray()
            ),
            "The query parameters didn't match as expected"
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

        } //end if request can have post / files

        $this->assertEquals(
            $method,
            $request->getMethod(),
            "The HTTP method is wrong"
        );
        $this->assertEquals(
            Config::get('transporter.driver'),
            last(explode('/', $request->getHeader('Accept'))),
            "The transport language is wrong"
        );
    }
}
