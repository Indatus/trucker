<?php

require_once __DIR__.'/../test_helpers/GuzzleTestingTrait.php';

use Trucker\Facades\RequestFactory;

class RawRequestMethodsTest extends TruckerTests
{

    use GuzzleTestingTrait;


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
                'trucker::base_uri' => $base_uri,
                'trucker::request_driver' => 'rest'
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
        $request = RequestFactory::build();
        $rawResponse = $request->rawGet($uri, $queryParams);

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
        //some vars for our test
        $uri           = '/users';
        $base_uri      = 'http://some-api.com';
        $postParams   = ['foo' => 'bar', 'biz' => 'bang'];
        $response_body = json_encode(['id' => 123, 'name' => 'foo']);

        //mock the response we expect
        $this->mockHttpResponse(
            //
            //config overrides & return client
            //
            $this->initGuzzleRequestTest([
                'trucker::base_uri' => $base_uri,
                'trucker::request_driver' => 'rest'
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
        $request = RequestFactory::build();
        $rawResponse = $request->rawPost($uri, $postParams);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();


        $this->makeGuzzleAssertions(
            'POST',
            $base_uri,
            $uri,
            [],
            $postParams
        );

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue(
            $rawResponse instanceof \Trucker\Responses\RawResponse
        );
    }



    public function testRawPut()
    {
        //some vars for our test
        $uri           = '/users/1';
        $base_uri      = 'http://some-api.com';
        $postParams   = ['foo' => 'bar', 'biz' => 'bang'];
        $response_body = json_encode(['id' => 123, 'name' => 'foo']);

        //mock the response we expect
        $this->mockHttpResponse(
            //
            //config overrides & return client
            //
            $this->initGuzzleRequestTest([
                'trucker::base_uri' => $base_uri,
                'trucker::request_driver' => 'rest'
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
        $request = RequestFactory::build();
        $rawResponse = $request->rawPut($uri, $postParams);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();


        $this->makeGuzzleAssertions(
            'PUT',
            $base_uri,
            $uri,
            [],
            $postParams
        );

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue(
            $rawResponse instanceof \Trucker\Responses\RawResponse
        );
    }



    public function testRawPatch()
    {
        //some vars for our test
        $uri           = '/users/1';
        $base_uri      = 'http://some-api.com';
        $postParams   = ['foo' => 'bar', 'biz' => 'bang'];
        $response_body = json_encode(['id' => 123, 'name' => 'foo']);

        //mock the response we expect
        $this->mockHttpResponse(
            //
            //config overrides & return client
            //
            $this->initGuzzleRequestTest([
                'trucker::base_uri' => $base_uri,
                'trucker::request_driver' => 'rest'
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
        $request = RequestFactory::build();
        $rawResponse = $request->rawPatch($uri, $postParams);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();


        $this->makeGuzzleAssertions(
            'PUT',
            $base_uri,
            $uri,
            [],
            $postParams
        );

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue(
            $rawResponse instanceof \Trucker\Responses\RawResponse
        );
    }



    public function testRawDelete()
    {
        //some vars for our test
        $uri           = '/users/1';
        $base_uri      = 'http://some-api.com';
        $queryParams   = ['foo' => 'bar', 'biz' => 'bang'];
        $response_body = json_encode(['id' => 123, 'name' => 'foo']);

        //mock the response we expect
        $this->mockHttpResponse(
            //
            //config overrides & return client
            //
            $this->initGuzzleRequestTest([
                'trucker::base_uri' => $base_uri,
                'trucker::request_driver' => 'rest'
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
        $request = RequestFactory::build();
        $rawResponse = $request->rawDelete($uri, $queryParams);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();


        $this->makeGuzzleAssertions(
            'DELETE',
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
}
