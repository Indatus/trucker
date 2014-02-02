<?php

use Trucker\Facades\Request;

class RestRequestTest extends TruckerTests
{

    public function testGetOption()
    {
        $config = Mockery::mock('Illuminate\Config\Repository');
        $config->shouldIgnoreMissing();
        $config->shouldReceive('get')->with('trucker::transporter')
            ->andReturn('json');

        $app = Mockery::mock('Illuminate\Container\Container');
        $app->shouldIgnoreMissing();
        $app->shouldReceive('offsetGet')->with('config')->andReturn($config);

        $request = new \Trucker\Requests\RestRequest($app);
        $transporter = $request->getOption('transporter');

        $this->assertEquals('json', $transporter);

    }

    public function testParseResponseToData()
    {

    }

    public function testSetTransportLanguage()
    {
        $mockRequest = Mockery::mock('Guzzle\Http\Message\Request');
        $mockRequest->shouldReceive('setHeader')
            ->with('Accept', 'application/json');

        $client = Mockery::mock('Guzzle\Http\Client');
        //$client->shouldIgnoreMissing();
        $client->shouldReceive('setBaseUrl')->with('http://example.com');
        $client->shouldReceive('get')->with('/users')->andReturn($mockRequest);

        $request = new \Trucker\Requests\RestRequest($this->app, $client);

        $r = $request->createRequest(
            'http://example.com',
            '/users',
            'GET'
        );
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

    public function testSettingModelProperties()
    {

    }

    public function testAddingErrorHandler()
    {

    }

    public function testBasicAuth()
    {
        
    }

    public function testSettingHeaders()
    {

    }

    public function testAddQueryCondition()
    {

    }

    public function testAddQueryResultOrder()
    {

    }

    public function testHttpMethodParam()
    {
        
    }

    public function testResponseWithCollectionKey()
    {

    }

    public function testResponseWithoutCollectionKey()
    {

    }

    public function testResponseWithErrorKey()
    {
        
    }
}
