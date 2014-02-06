<?php

use Trucker\Facades\UrlGenerator;
use Trucker\Facades\Trucker;
use Mockery as m;

class UrlGeneratorTest extends TruckerTests
{

    public function testResourceNaming()
    {
        //test resource name w/ reflection & inflection
        $x = new User;
        $this->assertEquals('User', $x->getResourceName());

        //test custom resource name
        $this->simulateSetInaccessableProperty($x, 'resourceName', 'Person');
        $this->assertEquals('Person', $x->getResourceName());
    }



    public function testGetUri()
    {
        //test custom uri setting
        $x = new User;
        $this->simulateSetInaccessableProperty($x, 'uri', '/some_other_uri');
        $this->assertEquals(
            '/some_other_uri',
            UrlGenerator::getURI($x)
        );

        //test multi-word class / uri
        $y = new UserPreferenceSetting;
        $this->assertEquals(
            '/user_preference_settings',
            UrlGenerator::getURI($y)
        );
    }



    public function testGetCollectionUri()
    {
        //test collection URI w/ inflection
        $x = new User;
        $this->assertEquals(
            '/users',
            UrlGenerator::getCollectionUri($x)
        );


        //test nestedUnder
        $x->nestedUnder = 'Company:100';
        $this->assertEquals(
            '/companies/100/users',
            UrlGenerator::getCollectionUri($x)
        );
        $x->nestedUnder = null;


        //test collection URI w/ custom resource name
        $this->simulateSetInaccessableProperty($x, 'resourceName', 'Person');
        $this->assertEquals(
            '/people',
            UrlGenerator::getCollectionUri($x)
        );

        //test collection URI w/ replacement
        $this->simulateSetInaccessableProperty($x, 'uri', '/collection/:group_id/people');
        $this->assertEquals(
            '/collection/1234/people',
            UrlGenerator::getCollectionUri($x, [':group_id' => 1234])
        );
    }



    public function testInstanceUpdateDeleteURIs()
    {
        $x = new User;
        $x->__set('id', 1234);
        $this->assertEquals('/users/:id', UrlGenerator::getInstanceUri($x));
        $this->assertEquals(
            '/users/1234',
            UrlGenerator::getInstanceUri($x, [':id' => 1234])
        );

        $this->assertEquals(
            '/users/1234',
            UrlGenerator::getUpdateUri($x, [':id' => 1234])
        );

        $this->assertEquals(
            '/users/1234',
            UrlGenerator::getDeleteUri($x, [':id' => 1234])
        );
    }



    public function testCreateUri()
    {
        $x = new User;
        $this->assertEquals(
            '/users',
            UrlGenerator::getCreateUri($x)
        );
    }

    public function testAppGetter()
    {
        $app = m::mock('Illuminate\Container\Container');
        $urlGen = new \Trucker\UrlGenerator($app);
        $this->assertEquals(
            $app,
            $urlGen->getApp()
        );
    }
}
