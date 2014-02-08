<?php

use Trucker\Responses\Collection;

use Mockery as m;

class CollectionTest extends TruckerTests
{

    public function testIteratorRewind()
    {
        $c = $this->getTestObject();
        $r = $c->rewind();
        $this->assertEquals(
            1234,
            $r->id
        );
        $this->assertTrue($r instanceof Trucker\Model);
    }



    public function testIteratorCurrent()
    {
        $c = $this->getTestObject();
        $c->next();
        $cur = $c->current();
        $this->assertEquals(
            1235,
            $cur->id
        );
        $this->assertTrue($cur instanceof Trucker\Model);
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
        $next = $c->next();
        $this->assertEquals(
            1235,
            $next->id
        );
        $this->assertTrue($next instanceof Trucker\Model);
    }



    public function testIteratorValid()
    {
        $c = $this->getTestObject();
        $this->assertTrue($c->valid(), "Expected valid() to be true");

        $x = new Collection([]);
        $this->assertFalse($x->valid(), 'Expected valid() to be false');
    }



    public function testSizeGetter()
    {
        $c = $this->getTestObject();
        $this->assertEquals(5, $c->size());
    }



    public function testFirstGetter()
    {
        $c = $this->getTestObject();
        $first = $c->first();
        $this->assertEquals(1234, $first->id);
        $this->assertTrue($first instanceof Trucker\Model);

        $c = new Collection([]);
        $this->assertEquals(null, $c->first());
    }



    public function testLastGetter()
    {
        $c = $this->getTestObject();
        $last = $c->last();
        $this->assertEquals(1238, $last->id);
        $this->assertTrue($last instanceof Trucker\Model);


        $c = new Collection([]);
        $this->assertEquals(null, $c->last());
    }



    public function testToArray()
    {
        $c = $this->getTestObject();
        $this->assertEquals(
            $this->getRecordsArray(),
            $c->toArray()
        );

        $c = $this->getTestObject(true);
        $this->assertEquals(
            [
                'collection' => $this->getRecordsArray(),
                'meta' => $this->getMetaArray()
            ],
            $c->toArray('collection', 'meta')
        );
    }



    public function testToJson()
    {
        $c = $this->getTestObject();
        $this->assertEquals(
            json_encode($this->getRecordsArray()),
            $c->toJson()
        );

        $c = $this->getTestObject(true);
        $this->assertEquals(
            json_encode([
                'collection' => $this->getRecordsArray(),
                'meta' => $this->getMetaArray()
            ]),
            $c->toJson('collection', 'meta')
        );
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
        $records = $this->getRecordsArray();

        $objects = [];
        foreach ($records as $r) {
            $m = m::mock('Trucker\Model');
            $m->shouldReceive('getBase64Indicator')->andReturn('_base64');
            $m->id = $r['id'];
            $m->shouldReceive('attributes')->andReturn($r);
            $objects[] = $m;
        }

        $meta = $this->getMetaArray();

        $collection = new Collection($objects);

        if ($setMeta) {
            $collection->metaData = $meta;
        }

        return $collection;
    }



    /**
     * Testing function to create an array of 
     * data to test against
     * 
     * @return array
     */
    private function getRecordsArray()
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

        return $records;
    }



    /**
     * Testing function to create an array of 
     * meta data to test against
     * 
     * @return array
     */
    private function getMetaArray()
    {
        $meta = [
            'per_page' => 25,
            'num_pages' => 4,
            'page' => 1
        ];
        return $meta;
    }
}
