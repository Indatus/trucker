<?php

use Trucker\Facades\Request;

class RequestTest extends TruckerTests
{

    public function testParseResponseToData()
    {
        $this->assertEquals(1,2, 'My test broke the build');
    }

    public function testSetTransportLanguage()
    {

    }

    public function testParseResponseStringToObject()
    {

    }

    public function testCreateNewRequest()
    {

    }

    public function testSendRequest()
    {

    }

    public function testSetPropertiesOnRequest()
    {

    }

    public function testSetPostParameters()
    {

    }

    public function testSetGetParameters()
    {

    }

    public function testSetFileParameters()
    {

    }

    public function testRawGet()
    {
        //some vars for our test
        $uri           = '/users';
        $base_uri      = 'http://some-api.com';
        $queryParams   = ['foo' => 'bar', 'biz' => 'bang'];
        $response_body = json_encode(['id' => 123, 'name' => 'foo']);

        //mock the response we expect
        $this->mockHttpResponse(
            //
            //config overrides & return client
            //
            $this->initGuzzleRequestTest([
                'trucker::base_uri' => $base_uri
            ]),
            //
            //expcted status
            //
            200,
            //
            //HTTP response headers
            //
            [
                'Location'     => $base_uri.'/'.$uri,
                'Content-Type' => 'application/json'
            ],
            //
            //response to return
            //
            $response_body
        );
        
        //execute what we're testing
        $rawResponse = Request::rawGet($uri, $queryParams);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();


        $this->makeGuzzleAssertions(
            'GET',
            $base_uri,
            $uri,
            $queryParams
        );

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue(
            $rawResponse instanceof \Trucker\Responses\RawResponse
        );
    }

    public function testRawPost()
    {

    }

    public function testRawPut()
    {
        
    }

    public function testRawPatch()
    {
        
    }

    public function testRawDelete()
    {
        
    }


    /**
     * Helper function to setup for testing requests that leverage
     * the Request class and guzzle
     * 
     * @param  array  $config config overrides
     * @return Guzzle\Http\Client
     */
    private function &initGuzzleRequestTest($config = array())
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
    private function makeGuzzleAssertions(
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
