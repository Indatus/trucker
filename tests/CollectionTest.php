<?php

use Trucker\Responses\Collection;

class CollectionTest extends TruckerTests
{

    public function testIteratorRewind()
    {
        $c = $this->getTestObject();
        $this->assertEquals(
            1234,
            $c->rewind()['id']
        );
    }

    public function testIteratorCurrent()
    {
        $c = $this->getTestObject();
        $c->next();
        $this->assertEquals(
            1235,
            $c->current()['id']
        );
    }

    public function testIteratorKey()
    {
        $c = $this->getTestObject();
        $c->next();
        $this->assertEquals(
            1,
            $c->key()
        );
    }

    public function testIteratorNext()
    {
        $c = $this->getTestObject();
        $this->assertEquals(
            1235,
            $c->next()['id']
        );
    }

    public function testIteratorValid()
    {
        // $c = $this->getTestObject();
        // $this->assertTrue($c->valid(), "Expected valid() to be true");

        // $x = new Collection([null]);
        // $this->assertFalse($x->valid(), 'Expected valid() to be false');
    }

    public function testSizeGetter()
    {

    }

    public function testFirstGetter()
    {

    }

    public function testLastGetter()
    {

    }

    public function testToArray()
    {

    }

    public function testToJson()
    {

    }


    /**
     * Helper function to create a popuplated
     * collection object for testing
     * 
     * @param  boolean $setMeta wether or not to set meta data
     * @return Trucker\Responses\Collection
     */
    private function getTestObject($setMeta = false)
    {
        $records = [
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
        ];

        $meta = [
            'per_page' => 25,
            'num_pages' => 4,
            'page' => 1
        ];

        $collection = new Collection($records);

        if ($setMeta) {
            $collection->metaData = $meta;
        }

        return $collection;
    }
}
