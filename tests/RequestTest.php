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

    public function testRawRequest()
    {

    }

    public function testRawGet()
    {
        $uri = 'users';
        $base_uri = 'http://foo.com';
        $queryParams = ['foo' => 'bar', 'biz' => 'bang'];

        $this->swapConfig(['trucker::base_uri' => $base_uri]);

        $this->newHttpClient();
        $this->mockHttpResponse(
            200,
            array(
                'Location'     => $base_uri.'/'.$uri,
                'Content-Type' => 'application/json'
            ),
            json_encode(['id' => 123, 'name' => 'foo'])
        );


        $rawResponse = (new \Trucker\Request($this->app, $this->getHttpClient()))
            ->rawGet($uri, $queryParams);
        $tResponse = $rawResponse->getResponse();

        $history = $this->getHttpClientHistory();
        echo $history->getLastRequest();
        echo $history->getLastResponse();
        echo count($history);

        $this->assertTrue($tResponse->isSuccessful());
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
