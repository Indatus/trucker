<?php

use Mockery as m;

use Trucker\Transporters\JsonTransporter;

class JsonTransporterTest extends TruckerTests
{
    public function testSetsHeaderOnRequest()
    {
        $request = m::mock('Guzzle\Http\Message\Request');
        $request->shouldReceive('setHeaders')
            ->with([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
            ->once();

        $transporter = new JsonTransporter;
        $transporter->setHeaderOnRequest($request);
    }


    public function testParsesResponseToData()
    {
        $response = m::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('json')
            ->once();

        $transporter = new JsonTransporter;
        $transporter->parseResponseToData($response);
    }


    public function testParsesResponseStringToObject()
    {
        $response = m::mock('Guzzle\Http\Message\Response');
        $response->shouldReceive('getBody')
            ->with(true)
            ->once()
            ->andReturn('{"foo":"bar", "biz":"bang"}');

        $transporter = new JsonTransporter;
        $result = $transporter->parseResponseStringToObject($response);

        $this->assertEquals("bar", $result->foo);
        $this->assertEquals("bang", $result->biz);
        $this->assertTrue(is_object($result), "Expected result to be an object");
    }


    public function testSetRequestBody()
    {
        $body = ['testme!'];
        $request = m::mock('Trucker\Requests\RestRequest');
        $request->shouldReceive('setBody')
            ->with(json_encode($body), 'application/json')
            ->once();

        $transporter = new JsonTransporter;
        $transporter->setRequestBody($request, $body);
    }
}
