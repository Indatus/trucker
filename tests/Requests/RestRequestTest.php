<?php

use Guzzle\Http\EntityBody;
use Mockery as m;
use Trucker\Facades\Config;
use Trucker\Facades\Request;

class RestRequestTest extends TruckerTests
{

    public function testGetOption()
    {
        $config = m::mock('Illuminate\Config\Repository');
        $config->shouldIgnoreMissing();
        $config->shouldReceive('get')->with('trucker::transporter.driver')
               ->andReturn('json');

        $app = m::mock('Illuminate\Container\Container');
        $app->shouldIgnoreMissing();
        $app->shouldReceive('offsetGet')->with('config')->andReturn($config);

        $request = new \Trucker\Requests\RestRequest($app);
        $transporter = Config::get('transporter.driver');

        $this->assertEquals('json', $transporter);
    }

    public function testSetTransportLanguage()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
        ]);

        $r = $request->createRequest('http://example.com', '/users', 'GET');
    }

    public function testCreateNewRequest()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
        ]);
        $result = $request->createRequest('http://example.com', '/users', 'GET');
        $this->assertTrue($result instanceof \Guzzle\Http\Message\Request);
    }

    public function testSetPostParameters()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'setPostField', 'args' => ['biz', 'banng']],
        ]);

        $request->createRequest('http://example.com', '/users', 'GET');
        $request->setPostParameters(['biz' => 'banng']);
    }

    public function testSetGetParameters()
    {
        $mQuery = m::mock('Guzzle\Http\QueryString');
        $mQuery->shouldReceive('add')->with('foo', 'bar');

        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'getQuery', 'return' => $mQuery],
        ]);

        $request->createRequest('http://example.com', '/users', 'GET');
        $request->setGetParameters(['foo' => 'bar']);
    }

    public function testSetFileParameters()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'addPostFile', 'args' => ['fileOne', '/path/to/fileOne']],
        ]);

        $request->createRequest('http://example.com', '/users', 'GET');
        $request->setFileParameters(['fileOne' => '/path/to/fileOne']);
    }

    public function testSettingModelProperties()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'setPostField', 'args' => ['foo', 'bar']],
            ['method' => 'setPostField', 'args' => ['biz', 'bang']],
            ['method' => 'addPostFile', 'args' => ['fOne', '/path/to/file/one']],
            ['method' => 'addPostFile', 'args' => ['fTwo', '/path/to/file/two']],
        ]);

        $attributes = [
            'foo' => 'bar',
            'biz' => 'bang',
            'roOne' => 'roOneVal',
            'roTwo' => 'roTwoVal',
            'fOne' => '/path/to/file/one',
            'fTwo' => '/path/to/file/two',
        ];

        $mUser = m::mock('User');
        $mUser->shouldReceive('getReadOnlyFields')->andReturn(['roOne', 'roTwo']);
        $mUser->shouldReceive('attributes')->andReturn($attributes);
        $mUser->shouldReceive('getFileFields')->andReturn(['fOne', 'fTwo']);

        $request->createRequest('http://example.com', '/users', 'GET');
        $request->setModelProperties($mUser);
    }

    public function testSettingHeaders()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'setHeader', 'args' => ['Cache-Control', 'no-cache, must-revalidate']],
        ]);

        $headers = ['Cache-Control' => 'no-cache, must-revalidate'];
        $request->createRequest('http://example.com', '/users', 'GET', $headers);
    }

    public function testSettingBody()
    {
        $request = \Guzzle\Http\Message\RequestFactory::getInstance()->create('PUT', 'http://www.test.com/');
        $request->setBody(EntityBody::factory('test'));
        $this->assertEquals(4, (string) $request->getHeader('Content-Length'));
        $this->assertFalse($request->hasHeader('Transfer-Encoding'));
    }

    public function testAddingErrorHandler()
    {
        $dispatcher = m::mock('Symfony\Component\EventDispatcher\EventDispatcher');
        $dispatcher->shouldReceive('addListener');

        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'getEventDispatcher', 'return' => $dispatcher],
        ]);

        $func = function ($event, $request) {
            return true;
        };

        $r = $request->createRequest('http://example.com', '/users', 'GET');
        $request->addErrorHandler(200, $func, true);
    }

    public function testAddQueryCondition()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
        ]);
        $r = $request->createRequest('http://example.com', '/users', 'GET');

        $c = m::mock('Trucker\Finders\Conditions\QueryConditionInterface');
        $c->shouldReceive('addToRequest')->with($r)->once();

        $request->addQueryCondition($c);
    }

    public function testAddQueryResultOrder()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
        ]);
        $r = $request->createRequest('http://example.com', '/users', 'GET');

        $o = m::mock('Trucker\Finders\Conditions\QueryResultOrderInterface');
        $o->shouldReceive('addToRequest')->with($r)->once();

        $request->addQueryResultOrder($o);
    }

    public function testAddAuthentication()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
        ]);
        $r = $request->createRequest('http://example.com', '/users', 'GET');

        $auth = m::mock('Trucker\Requests\Auth\AuthenticationInterface');
        $auth->shouldReceive('authenticateRequest')->with($r)->once();

        $request->authenticate($auth);
    }

    public function testSendRequest()
    {
        $request = $this->simpleMockRequest([
            [
                'method' => 'setHeaders',
                'args' => [[
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ]],
            ],
            ['method' => 'send', 'return' => m::mock('Guzzle\Http\Message\Response')],
        ]);

        $request->createRequest('http://example.com', '/users', 'GET');
        $request->sendRequest();
    }

    public function testHttpMethodParam()
    {
        $request = $this->simpleMockRequest(
            [
                [
                    'method' => 'setHeaders',
                    'args' => [[
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ]],
                ],
                ['method' => 'setPostField', 'args' => ['_method', 'PUT']],
            ],
            'http://example.com',
            '/users/1',
            'post'
        );

        $request->createRequest('http://example.com', '/users/1', 'PUT', [], '_method');
    }

    /**
     * Function to create and return a Trucker\Requests\RestRequest object
     * with mock client & rquest objects injected
     *
     * @param  array $shouldReceive
     * @param  string $baseUrl
     * @param  string $uri
     * @param  string $method
     * @return Trucker\Requests\RestRequest
     */
    private function simpleMockRequest(
        $shouldReceive = [],
        $baseUrl = 'http://example.com',
        $uri = '/users',
        $method = 'get'
    ) {

        $mockRequest = m::mock('Guzzle\Http\Message\Request');

        foreach ($shouldReceive as $sr) {

            $mr = $mockRequest->shouldReceive($sr['method']);

            if (array_key_exists('args', $sr)) {
                call_user_func_array([$mr, 'with'], $sr['args']);
            }

            if (array_key_exists('return', $sr)) {
                $mr->andReturn($sr['return']);
            }

            $mr->times(array_key_exists('times', $sr) ? $sr['times'] : 1);
        }

        $client = m::mock('Guzzle\Http\Client');
        $client->shouldReceive('setBaseUrl')->with($baseUrl);
        $client->shouldReceive($method)->with($uri)->andReturn($mockRequest);

        $request = new \Trucker\Requests\RestRequest($this->app, $client);
        return $request;
    }
}
