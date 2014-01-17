<?php

use Trucker\Facades\Trucker;

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

    public function testConfigDefaultsAreLoaded()
    {
        $t = $this->app->make('trucker.model');
    }

    public function testGetters()
    {

    }


    // public function testSetters(){}

    // public function testInflateFromArray(){}

    // public function testConstructorInflateFromArray(){}

    // public function testBasicFindAll(){}

    // public function testFindAllWithConditions(){}

    // public function testFullFindAll(){}

    // public function testFindById(){}

    // public function testGetId(){}

    // public function testSaveFunction(){}

    // public function testCreateShouldSave(){}

    // public function testCreateShouldFail(){}

    // public function testUpdateShouldSave(){}

    // public function testUpdateShouldFail(){}

    // public function testDestroy(){}
}
