<?php

require_once __DIR__.'/stubs/User.php';
use Trucker\Responses\Collection;
use Trucker\Facades\GetArrayQueryCondition;
use Trucker\Facades\GetArrayResultOrder;

class CollectionFinderTest extends TruckerTests
{

    public function testFindAll()
    {
        $this->setupIndividualTest($this->getTestOptions());
        extract($this->getTestOptions());

        $found = User::all();

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();

        $this->makeGuzzleAssertions('GET', $base_uri, $uri);

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->size(), "expected count is wrong");
        $this->assertEquals(1234, $found->first()->id);
        $this->assertEquals('John Doe', $found->first()->name);
    }



    public function testFindAllWithGetParams()
    {
        $this->setupIndividualTest($this->getTestOptions());
        extract($this->getTestOptions());

        $found = User::all(null, null, $queryParams);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();

        $this->makeGuzzleAssertions('GET', $base_uri, $uri, $queryParams);

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->size(), "expected count is wrong");
        $this->assertEquals(1234, $found->first()->id);
        $this->assertEquals('John Doe', $found->first()->name);
    }


    public function testFindAllWithGetParamsQueryConditions()
    {
        $this->setupIndividualTest($this->getTestOptions());
        extract($this->getTestOptions());

        $conditions = GetArrayQueryCondition::newInstance();
        $conditions->addCondition('name', '=', 'John Doe');
        $conditions->addCondition('email', '=', 'jdoe@noboddy.com');
        $conditions->addCondition('id', '>=', 100);
        $conditions->setLogicalOperator($conditions->getLogicalOperatorAnd());

        $found = User::all($conditions);

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();

        $this->makeGuzzleAssertions('GET', $base_uri, $uri, $conditions->toArray());

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->size(), "expected count is wrong");
        $this->assertEquals(1234, $found->first()->id);
        $this->assertEquals('John Doe', $found->first()->name);
    }


    public function testFindAllWithGetParamsQueryConditionsAndGetOrderResults()
    {
        $this->setupIndividualTest($this->getTestOptions());
        extract($this->getTestOptions());

        $conditions = GetArrayQueryCondition::newInstance();
        $conditions->addCondition('name', '=', 'John Doe');
        $conditions->addCondition('email', '=', 'jdoe@noboddy.com');
        $conditions->addCondition('id', '>=', 100);
        $conditions->setLogicalOperator($conditions->getLogicalOperatorAnd());

        $order = GetArrayResultOrder::newInstance();
        $order->setOrderByField('email');
        $order->setOrderDirection($order->getOrderDirectionDescending());

        $found = User::all($conditions, $order);

        $getParams = array_merge($conditions->toArray(), $order->toArray());

        //get objects to assert on
        $history     = $this->getHttpClientHistory();
        $request     = $history->getLastRequest();
        $response    = $history->getLastResponse();

        $this->makeGuzzleAssertions('GET', $base_uri, $uri, $getParams);

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->size(), "expected count is wrong");
        $this->assertEquals(1234, $found->first()->id);
        $this->assertEquals('John Doe', $found->first()->name);
    }


    /**
     * Helper function to get commonly used testing data
     * 
     * @return array
     */
    private function getTestOptions()
    {
        //some vars for our test
        $data                  = [];
        $data['uri']           = '/users';
        $data['base_uri']      = 'http://example.com';
        $data['queryParams']   = ['foo' => 'bar', 'biz' => 'bang'];
        $data['response_body'] = json_encode(
            [
                [
                    'id'    => 1234,
                    'name'  => 'John Doe',
                    'email' => 'jdoe@noboddy.com'
                ],
                [
                    'id'    => 1235,
                    'name'  => 'Sammy Smith',
                    'email' => 'sammys@mysite.com'
                ],
                [
                    'id'    => 1236,
                    'name'  => 'Tommy Jingles',
                    'email' => 'tjingles@gmail.com'
                ],
                [
                    'id'    => 1237,
                    'name'  => 'Brent Sanders',
                    'email' => 'bsanders@yahoo.com'
                ],
                [
                    'id'    => 1238,
                    'name'  => 'Michael Blanton',
                    'email' => 'mblanton@outlook.com'
                ],
            ]
        );

        return $data;
    }


    /**
     * Function to mock a request for us and 
     * expect test data from our getTestOptions() function
     * 
     * @param  array $options 
     * @return void
     */
    private function setupIndividualTest($options = [])
    {
        extract($options);

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
    }
}
