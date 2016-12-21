<?php

use Mockery as m;
use Trucker\Facades\Config;
use Trucker\Facades\Trucker;

class ModelTest extends TruckerTests
{

    use GuzzleTestingTrait;

    public function testAppMake()
    {
        $t = $this->app->make('trucker.model');
        $this->assertEquals($t->attributes(), array());
    }

    public function testFacade()
    {
        $this->assertEquals(Trucker::attributes(), array());
    }

    public function testSimplePropertyGettersAndSetters()
    {
        $t = $this->app->make('trucker.model');
        $t->foo = 'bar';
        $this->assertEquals('bar', $t->foo);
        $this->assertTrue(array_key_exists('foo', $t->attributes()), 'set value missing from attributes array');
    }

    public function testFillWithGuarded()
    {
        //TEST fill via constructor
        $t = Trucker::newInstance(['foo' => 'bar']);

        $attrs = $t->attributes();
        $this->assertEquals('bar', $t->foo);
        $this->assertTrue(array_key_exists('foo', $attrs), 'set value missing from attributes array');

        //TEST fill via method
        $t->fill(['biz' => 'bang']);
        $this->assertEquals('bang', $t->biz);
        $this->assertTrue(array_key_exists('biz', $t->attributes()), 'set value missing from attributes array');

        //simulate that the guarded field was set in the class
        $this->simulateSetInaccessableProperty($t, 'guarded', 'one,two');

        //TEST fill with guarded properties
        $t->fill(['one' => 1, 'two' => 2, 'three' => 3]);
        $this->assertEquals(null, $t->one);
        $this->assertEquals(null, $t->two);
        $this->assertEquals(3, $t->three);
    }

    public function testBase64PropertySetterMutators()
    {
        $t = Trucker::newInstance();

        //TEST fill with base64 property
        $this->simulateSetInaccessableProperty($t, 'fileFields', 'meme,other_meme');
        $testImagePath = __DIR__ . '/../fixtures/test-all-things.jpg';
        $md5 = md5_file($testImagePath);
        $imgData = file_get_contents($testImagePath);
        $base64Image = base64_encode($imgData);

        //set values
        $t->fill(['meme_base64' => $base64Image]);
        $t->other_meme_base64 = $base64Image;

        //get resulted values
        $tmp1 = $t->meme;
        $tmp2 = $t->other_meme;

        $this->assertTrue(file_exists($tmp1), 'fill(): base64 decoded file is missing');
        $md5Tmp1 = md5_file($tmp1);
        $this->assertEquals($md5, $md5Tmp1, 'fill(): md5 of base64 decoded file is wrong');
        unlink($tmp1); //remove the test generated file

        $this->assertTrue(file_exists($tmp2), '__set(): base64 decoded file is missing');
        $md5Tmp2 = md5_file($tmp2);
        $this->assertEquals($md5, $md5Tmp2, '__set(): md5 of base64 decoded file is wrong');
        unlink($tmp2); //remove the test generated file
    }

    public function testPostRequestCleanUp()
    {
        $t = Trucker::newInstance();

        //create our test files and assert that they exist
        $path = __DIR__ . '/../fixtures';
        $tmp1 = $path . '/' . time() . '-' . rand() . '.txt';
        $tmp2 = $path . '/' . time() . '-' . rand() . '.txt';
        file_put_contents($tmp1, "Data for {$tmp1}");
        file_put_contents($tmp2, "Data for {$tmp1}");

        $this->assertTrue(file_exists($tmp1), 'Expected {$tmp1} to exist prior to test');
        $this->assertTrue(file_exists($tmp2), 'Expected {$tmp2} to exist prior to test');

        //setup the instance to have these files for cleanup
        $this->simulateSetInaccessableProperty($t, 'postRequestCleanUp', [$tmp1, $tmp2]);

        $this->invokeInaccessibleMethod($t, 'doPostRequestCleanUp');

        $this->assertFalse(file_exists($tmp1), 'Expected {$tmp1} to have been unlinked');
        $this->assertFalse(file_exists($tmp2), 'Expected {$tmp2} to have been unlinked');
    }

    public function testGetUri()
    {
        $u = Trucker::newInstance();
        $this->assertNull($u->getURI());

        $this->simulateSetInaccessableProperty($u, 'uri', 'employees');

        $this->assertEquals('employees', $u->getURI());
    }

    public function testGetResourceName()
    {
        $u = new User;
        $this->assertEquals('User', $u->getResourceName());

        $this->simulateSetInaccessableProperty($u, 'resourceName', 'Employee');

        $this->assertEquals('Employee', $u->getResourceName());
    }

    public function testGetMutableFields()
    {
        $u = Trucker::newInstance(['foo' => 'bar', 'biz' => 'bang']);

        $this->simulateSetInaccessableProperty($u, 'readOnlyFields', 'biz,bang');
        $this->assertTrue(
            $this->arraysAreSimilar(['foo' => 'bar'], $u->getMutableFields()),
            'Mutable fields were not as expected'
        );
    }

    public function testGetReadOnlyFields()
    {
        $u = Trucker::newInstance();

        $this->simulateSetInaccessableProperty($u, 'readOnlyFields', 'biz,bang');
        $this->assertTrue(
            $this->arraysAreSimilar(['biz'], $u->getReadOnlyFields()),
            "Read only fields were not as expected"
        );
    }

    public function testGetIdentityProperty()
    {
        $u = Trucker::newInstance();
        $this->assertEquals('id', $u->getIdentityProperty());

        $this->swapConfig(['trucker::resource.identity_property' => 'user_id']);
        Config::setApp($this->app);

        $u = Trucker::newInstance();
        $this->assertEquals('user_id', $u->getIdentityProperty());
    }

    public function testGetFileFields()
    {
        $u = Trucker::newInstance();

        $this->simulateSetInaccessableProperty($u, 'fileFields', 'fooFile,bizFile');
        $this->assertTrue(
            $this->arraysAreSimilar(['fooFile', 'bizFile'], $u->getFileFields()),
            'Returned file fields were not as expected'
        );
    }

    public function testGetGuardedAttributes()
    {
        $u = Trucker::newInstance();

        $this->simulateSetInaccessableProperty($u, 'identityProperty', 'id');
        $this->simulateSetInaccessableProperty($u, 'guarded', 'biz,bang');
        $this->assertTrue(
            $this->arraysAreSimilar(['biz', 'bang', 'id'], $u->getGuardedAttributes()),
            'Guarded attributes were not as expected'
        );
    }

    public function testErrorsGetter()
    {
        $u = Trucker::newInstance();

        $this->simulateSetInaccessableProperty($u, 'errors', ['foo', 'bar']);
        $this->assertTrue(
            $this->arraysAreSimilar(['foo', 'bar'], $u->errors()),
            'Errors array was not as expected'
        );
    }

    public function testAttributesGetter()
    {
        $attrs = ['foo' => 'bar', 'biz' => 'bang'];
        $u = Trucker::newInstance($attrs);

        $this->assertTrue(
            $this->arraysAreSimilar($attrs, $u->attributes()),
            'Attributes array was not as expected'
        );
    }

    public function testUnsetFunction()
    {
        $attrs = ['foo' => 'bar', 'biz' => 'bang'];
        $u = Trucker::newInstance($attrs);

        $this->assertTrue(
            $this->arraysAreSimilar($attrs, $u->attributes()),
            'Attributes array was not as expected'
        );

        $u->__unset('biz');

        $this->assertTrue(
            $this->arraysAreSimilar(['foo' => 'bar'], $u->attributes()),
            'Attributes array was not as expected after __unset'
        );
    }

    public function testGetId()
    {
        $t = Trucker::newInstance();
        $this->simulateSetInaccessableProperty($t, 'identityProperty', 'id');
        $t->id = 123456;
        $this->assertEquals(123456, $t->getId(), 'identity property could not properly resolve');
    }

    public function testCreateShouldSaveWithoutHttpMethodParam()
    {
        //setup our creation mocks, expected results etc
        $this->setupIndividualTest($this->getCreateTestOptions());

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($result, "Save() should have been true");
        $this->assertEquals('POST', $request->getMethod(), "POST method expected");

        $this->assertTrue(
            $this->arraysAreSimilar(
                $u->attributes(),
                array_merge($request->getPostFields()->toArray(), ['id' => 1])
            ),
            "Expected post params to be equal to attributes"
        );
        $this->assertEquals('/users', $request->getPath(), "Expected request to go to /users");
        $this->assertEquals(1, $u->getId(), "Expected respose to set ID");
    }

    public function testCreateShouldSaveWithHttpMethodParam()
    {
        //setup our creation mocks, expected results etc
        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::request.http_method_param' => '_method',
        ];
        $this->setupIndividualTest($this->getCreateTestOptions(), $config);

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($result, "Save() should have been true");
        $this->assertEquals('POST', $request->getMethod(), "POST method expected");
        $this->assertTrue(
            $this->arraysAreSimilar(
                $u->attributes(),
                array_merge($request->getPostFields()->toArray(), ['id' => 1])
            ),
            "Expected post params to be equal to attributes"
        );
        $this->assertEquals('/users', $request->getPath(), "Expected request to go to /users");
        $this->assertEquals(1, $u->getId(), "Expected respose to set ID");
        $this->assertArrayHasKey('_method', $request->getPostFields()->toArray(), 'Expected http method param');
    }

    public function testCreateShouldFailWithErrorsKey()
    {
        $invalid_status = Config::get('response.http_status.invalid');

        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::error_handler.driver' => 'parameter_key',
            'trucker::error_handler.errors_key' => 'errors',
        ];

        //setup our creation mocks, expected results etc
        $this->setupIndividualTest(
            $this->getSaveErrorTestOptions(
                Config::get('error_handler.errors_key')
            ),
            $config,
            $invalid_status
        );

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertEquals(
            $invalid_status,
            $response->getStatusCode(),
            "Expected different response code"
        );
        $this->assertCount(2, $u->errors(), 'Expected 2 errors');
    }

    public function testCreateShouldFailWithoutErrorsKey()
    {
        $invalid_status = Config::get('response.http_status.invalid');

        //setup our creation mocks, expected results etc
        $this->setupIndividualTest(
            $this->getSaveErrorTestOptions(),
            [],
            $invalid_status
        );

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertEquals(
            $invalid_status,
            $response->getStatusCode(),
            "Expected different response code"
        );
        $this->assertCount(2, $u->errors(), 'Expected 2 errors');
    }

    public function testUpdateShouldSaveWithoutHttpMethodParam()
    {
        //setup our creation mocks, expected results etc
        $this->setupIndividualTest($this->getUpdateTestOptions());

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $u->id = 1;
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($result, "Save() should have been true");
        $this->assertEquals('PUT', $request->getMethod(), "PUT method expected");

        $this->assertTrue(
            $this->arraysAreSimilar(
                $u->attributes(),
                array_merge($request->getPostFields()->toArray())
            ),
            "Expected post params to be equal to attributes"
        );
        $this->assertEquals('/users/1', $request->getPath(), "Expected request to go to /users/1");
    }

    public function testUpdateShouldSaveWithHttpMethodParam()
    {
        //setup our creation mocks, expected results etc
        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::request.http_method_param' => '_method',
        ];
        $this->setupIndividualTest($this->getUpdateTestOptions(), $config);

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $u->id = 1;
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($result, "Save() should have been true");

        $this->assertEquals('POST', $request->getMethod(), "POST method expected");

        $this->assertTrue(
            $this->arraysAreSimilar(
                $u->attributes(),
                array_merge($request->getPostFields()->toArray())
            ),
            "Expected post params to be equal to attributes"
        );
        $this->assertEquals('/users/1', $request->getPath(), "Expected request to go to /users/1");
        $this->assertArrayHasKey('_method', $request->getPostFields()->toArray(), 'Expected http method param');
    }

    public function testUpdateShouldFailWithErrorsKey()
    {
        $invalid_status = Config::get('response.http_status.invalid');

        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::error_handler.driver' => 'parameter_key',
            'trucker::error_handler.errors_key' => 'errors',
        ];

        //setup our creation mocks, expected results etc
        $this->setupIndividualTest(
            $this->getSaveErrorTestOptions(
                Config::get('error_handler.errors_key')
            ),
            $config,
            $invalid_status
        );

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $u->id = 1;
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertFalse($result, 'Expected save to return false');
        $this->assertEquals(
            $invalid_status,
            $response->getStatusCode(),
            "Expected different response code"
        );
        $this->assertCount(2, $u->errors(), 'Expected 2 errors');
    }

    public function testUpdateShouldFailWithoutErrorsKey()
    {
        $invalid_status = Config::get('response.http_status.invalid');

        //setup our creation mocks, expected results etc
        $this->setupIndividualTest(
            $this->getSaveErrorTestOptions(),
            [],
            $invalid_status
        );

        $u = new User(['name' => 'John Doe', 'email' => 'jdoe@noboddy.com']);
        $u->id = 1;
        $result = $u->save();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertFalse($result, 'Expected save to return false');
        $this->assertEquals(
            $invalid_status,
            $response->getStatusCode(),
            "Expected different response code"
        );
        $this->assertCount(2, $u->errors(), 'Expected 2 errors');
    }

    public function testDestroyWithoutHttpMethodParam()
    {
        //setup our creation mocks, expected results etc
        $this->setupIndividualTest($this->getUpdateTestOptions());

        $u = new User;
        $u->id = 1;
        $result = $u->destroy();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($result, "destroy() should have been true");
        $this->assertEquals('DELETE', $request->getMethod(), "DELETE method expected");

        $this->assertEquals('/users/1', $request->getPath(), "Expected request to go to /users/1");
    }

    public function testDestroyWithHttpMethodParam()
    {
        //setup our creation mocks, expected results etc
        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::request.http_method_param' => '_method',
        ];
        $this->setupIndividualTest($this->getCreateTestOptions(), $config);

        $u = new User;
        $u->id = 1;
        $result = $u->destroy();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($result, "destroy() should have been true");
        $this->assertEquals('POST', $request->getMethod(), "POST method expected");
        $this->assertEquals('/users/1', $request->getPath(), "Expected request to go to /users/1");
        $this->assertArrayHasKey('_method', $request->getPostFields()->toArray(), 'Expected http method param');
    }

    public function testDestroyShouldFailWithErrorsKey()
    {
        $invalid_status = Config::get('response.http_status.invalid');

        $config = [
            'trucker::request.base_uri' => 'http://example.com',
            'trucker::error_handler.driver' => 'parameter_key',
            'trucker::error_handler.errors_key' => 'errors',
        ];

        //setup our creation mocks, expected results etc
        $this->setupIndividualTest(
            $this->getSaveErrorTestOptions(
                Config::get('error_handler.errors_key')
            ),
            $config,
            $invalid_status
        );

        $u = new User;
        $u->id = 1;
        $result = $u->destroy();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertFalse($result, 'Expected destroy() to return false');
        $this->assertEquals(
            $invalid_status,
            $response->getStatusCode(),
            "Expected different response code"
        );
        $this->assertCount(2, $u->errors(), 'Expected 2 errors');
    }

    public function testDestroyShouldFailWithoutErrorsKey()
    {
        $invalid_status = Config::get('response.http_status.invalid');

        //setup our creation mocks, expected results etc
        $this->setupIndividualTest(
            $this->getSaveErrorTestOptions(),
            [],
            $invalid_status
        );

        $u = new User;
        $u->id = 1;
        $result = $u->destroy();

        //get objects to assert on
        $history = $this->getHttpClientHistory();
        $request = $history->getLastRequest();
        $response = $history->getLastResponse();

        $this->assertFalse($result, 'Expected destroy() to return false');
        $this->assertEquals(
            $invalid_status,
            $response->getStatusCode(),
            "Expected different response code"
        );
        $this->assertCount(2, $u->errors(), 'Expected 2 errors');
    }

    /**
     * Helper function to get commonly used testing data
     * for creating an entity
     *
     * @return array
     */
    private function getCreateTestOptions()
    {
        //some vars for our test
        $data = [];
        $data['uri'] = '/users';
        $data['base_uri'] = 'http://example.com';
        $data['response_body'] = json_encode(
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'jdoe@noboddy.com',
            ]
        );

        return $data;
    }

    /**
     * Helper function to get commonly used testing data
     * for updating an entity
     *
     * @return array
     */
    private function getUpdateTestOptions()
    {
        //some vars for our test
        $data = [];
        $data['uri'] = '/users/1';
        $data['base_uri'] = 'http://example.com';
        $data['response_body'] = json_encode(
            [
                'id' => 1,
                'name' => 'John Doe',
                'email' => 'jdoe@noboddy.com',
            ]
        );

        return $data;
    }

    /**
     * Helper function to get commonly used testing data
     * for error testing entity saving
     *
     * @return array
     */
    private function getSaveErrorTestOptions($key = null)
    {

        $errors = [
            "Username can't be blank",
            "Email format is invalid",
        ];

        if ($key) {
            $errorsArray[$key] = $errors;
        } else {
            $errorsArray = $errors;
        }

        //some vars for our test
        $data = [];
        $data['uri'] = '/users/1';
        $data['base_uri'] = 'http://example.com';
        $data['response_body'] = json_encode($errorsArray);
        return $data;
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
