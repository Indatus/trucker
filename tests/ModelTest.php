<?php

require_once __DIR__.'/stubs/User.php';

use Trucker\Facades\Trucker;
use Trucker\Facades\Request;
use Mockery as m;

class ModelTest extends TruckerTests
{


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
        $testImagePath = __DIR__.'/fixtures/test-all-things.jpg';
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

        $this->swapConfig(['trucker::identity_property' => 'user_id']);
        Request::setApp($this->app);

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



    public function testCreateShouldSave()
    {

    }



    public function testCreateShouldFail()
    {

    }



    public function testUpdateShouldSave()
    {

    }



    public function testUpdateShouldFail()
    {

    }



    public function testDestroy()
    {

    }



    public function testSaveWithErrorsResponseKey()
    {
        
    }
}
