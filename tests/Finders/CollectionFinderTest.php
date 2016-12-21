<?php

use Mockery as m;
use Trucker\Facades\ConditionFactory;
use Trucker\Facades\Config;
use Trucker\Facades\ResultOrderFactory;
use Trucker\Responses\Collection;

class CollectionFinderTest extends TruckerTests
{
    use GuzzleTestingTrait;

    public function setUp()
    {
        parent::setUp();
        $this->swapConfig([]);
        Config::setApp($this->app);
    }

    public function testFindAll()
    {
        $this->setupIndividualTest($this->getTestOptions());
        extract($this->getTestOptions());

        $found = User::all();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

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
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

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

        $conditions = ConditionFactory::build();
        $conditions->addCondition('name', '=', 'John Doe');
        $conditions->addCondition('email', '=', 'jdoe@noboddy.com');
        $conditions->addCondition('id', '>=', 100);

        $this->assertEquals(
            http_build_query($conditions->toArray()),
            $conditions->toQueryString(),
            "Expected query string to look different"
        );

        $this->setExpectedException('InvalidArgumentException');
        $conditions->setLogicalOperator('invalid-operator');

        $conditions->setLogicalOperator($conditions->getLogicalOperatorAnd());

        $this->assertEquals(
            Config::get('query_condition.and_operator'),
            $conditions->getLogicalOperatorAnd()
        );

        $this->assertEquals(
            Config::get('query_condition.or_operator'),
            $conditions->getLogicalOperatorOr()
        );

        $found = User::all($conditions);

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

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

        $conditions = ConditionFactory::build();
        $conditions->addCondition('name', '=', 'John Doe');
        $conditions->addCondition('email', '=', 'jdoe@noboddy.com');
        $conditions->addCondition('id', '>=', 100);
        $conditions->setLogicalOperator($conditions->getLogicalOperatorAnd());

        $this->assertEquals(
            http_build_query($conditions->toArray()),
            $conditions->toQueryString(),
            "Expected query string to look different"
        );

        $order = ResultOrderFactory::build();
        $order->setOrderByField('email');
        $order->setOrderDirection($order->getOrderDirectionDescending());

        $this->assertEquals(
            http_build_query($order->toArray()),
            $order->toQueryString(),
            "Expected query string to look different"
        );

        $found = User::all($conditions, $order);

        $getParams = array_merge($conditions->toArray(), $order->toArray());

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->makeGuzzleAssertions('GET', $base_uri, $uri, $getParams);

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->size(), "expected count is wrong");
        $this->assertEquals(1234, $found->first()->id);
        $this->assertEquals('John Doe', $found->first()->name);
    }

    public function testFindAllWithCollectionKeyOption()
    {
        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::resource.collection_key' => 'collection',
        ];

        $this->setupIndividualTest(
            $this->getTestOptions('collection'),
            $config
        );

        extract($this->getTestOptions('collection'));

        $found = User::all();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->makeGuzzleAssertions('GET', $base_uri, $uri);

        //assert that the HTTP RESPONSE is what is expected
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals($response_body, $response->getBody(true));
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->size(), "expected count is wrong");
        $this->assertEquals(1234, $found->first()->id);
        $this->assertEquals('John Doe', $found->first()->name);
        $this->assertArrayHasKey(
            'collection',
            $found->toArray('collection'),
            'Excpected collection toArray() to have collection key'
        );
    }

    /**
     * Helper function to get commonly used testing data
     *
     * @return array
     */
    private function getTestOptions($collectionKey = null)
    {
        //some vars for our test
        $data = [];
        $data['uri'] = '/users';
        $data['base_uri'] = 'http://example.com';
        $data['queryParams'] = ['foo' => 'bar', 'biz' => 'bang'];
        $data['response_body'] = json_encode(
            $this->getRecords($collectionKey)
        );

        return $data;
    }

    private function getRecords($collectionKey = null)
    {
        $records = [
            [
                'id' => 1234,
                'name' => 'John Doe',
                'email' => 'jdoe@noboddy.com',
            ],
            [
                'id' => 1235,
                'name' => 'Sammy Smith',
                'email' => 'sammys@mysite.com',
            ],
            [
                'id' => 1236,
                'name' => 'Tommy Jingles',
                'email' => 'tjingles@gmail.com',
            ],
            [
                'id' => 1237,
                'name' => 'Brent Sanders',
                'email' => 'bsanders@yahoo.com',
            ],
            [
                'id' => 1238,
                'name' => 'Michael Blanton',
                'email' => 'mblanton@outlook.com',
            ],
        ];

        if ($collectionKey) {
            $result = [$collectionKey => $records];
        } else {
            $result = $records;
        }

        return $result;
    }

    /**
     * Function to mock a request for us and
     * expect test data
     *
     * @param  array $options
     * @param  array $config_overrides
     * @param  int   $status
     * @param  string $content_type
     * @return void
     */
    private function setupIndividualTest(
        $options = [],
        $config_overrides = [],
        $status = 200,
        $content_type = 'application/json'
    ) {

        extract($options);

        $config_overrides = empty($config_overrides) ? ['trucker::request.base_uri' => $base_uri] : $config_overrides;

        //mock the response we expect
        $this->mockHttpResponse(
            //
            //config overrides & return client
            //
            $this->initGuzzleRequestTest($config_overrides),
            //
            //expcted status
            //
            $status,
            //
            //HTTP response headers
            //
            [
                'Location' => $base_uri . '/' . $uri,
                'Content-Type' => $content_type,
            ],
            //
            //response to return
            //
            $response_body
        );
    }
}
