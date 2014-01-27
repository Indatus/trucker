<?php

use Trucker\Facades\Request;

class RequestTest extends TruckerTests
{

    public function testParseResponseToData()
    {

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
}
