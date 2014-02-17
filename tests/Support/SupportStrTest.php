<?php

use Trucker\Support\Str;

class SupportStrTest extends PHPUnit_Framework_TestCase
{

    /**
    * Test the Str::words method.
    *
    * @group laravel
    */
    public function testStringCanBeLimitedByWords()
    {
        $this->assertEquals('Taylor...', Str::words('Taylor Otwell', 1));
        $this->assertEquals('Taylor___', Str::words('Taylor Otwell', 1, '___'));
        $this->assertEquals('Taylor Otwell', Str::words('Taylor Otwell', 3));
    }


    public function testStringTrimmedOnlyWhereNecessary()
    {
        $this->assertEquals(' Taylor Otwell ', Str::words(' Taylor Otwell ', 3));
        $this->assertEquals(' Taylor...', Str::words(' Taylor Otwell ', 1));
    }

    public function testStringTitle()
    {
        $this->assertEquals('Jefferson Costella', Str::title('jefferson costella'));
        $this->assertEquals('Jefferson Costella', Str::title('jefFErson coSTella'));
    }

    public function testStringWithoutWordsDoesntProduceError()
    {
        $nbsp = chr(0xC2).chr(0xA0);
        $this->assertEquals(' ', Str::words(' '));
        $this->assertEquals($nbsp, Str::words($nbsp));
    }


    public function testStringMacros()
    {
        Trucker\Support\Str::macro(
            __CLASS__,
            function () {
                return 'foo';
            }
        );
        $this->assertEquals('foo', Str::SupportStrTest());
    }


    public function testStartsWith()
    {
        $this->assertTrue(Str::startsWith('jason', 'jas'));
        $this->assertTrue(Str::startsWith('jason', 'jason'));
        $this->assertTrue(Str::startsWith('jason', array('jas')));
        $this->assertFalse(Str::startsWith('jason', 'day'));
        $this->assertFalse(Str::startsWith('jason', array('day')));
        $this->assertFalse(Str::startsWith('jason', ''));
    }


    public function testEndsWith()
    {
        $this->assertTrue(Str::endsWith('jason', 'on'));
        $this->assertTrue(Str::endsWith('jason', 'jason'));
        $this->assertTrue(Str::endsWith('jason', array('on')));
        $this->assertFalse(Str::endsWith('jason', 'no'));
        $this->assertFalse(Str::endsWith('jason', array('no')));
        $this->assertFalse(Str::endsWith('jason', ''));
    }


    public function testStrContains()
    {
        $this->assertTrue(Str::contains('taylor', 'ylo'));
        $this->assertTrue(Str::contains('taylor', array('ylo')));
        $this->assertFalse(Str::contains('taylor', 'xxx'));
        $this->assertFalse(Str::contains('taylor', array('xxx')));
        $this->assertFalse(Str::contains('taylor', ''));
    }


    public function testParseCallback()
    {
        $this->assertEquals(array('Class', 'method'), Str::parseCallback('Class@method', 'foo'));
        $this->assertEquals(array('Class', 'foo'), Str::parseCallback('Class', 'foo'));
    }


    public function testSlug()
    {
        $this->assertEquals('hello-world', Str::slug('hello world'));
        $this->assertEquals('hello-world', Str::slug('hello-world'));
        $this->assertEquals('hello-world', Str::slug('hello_world'));
        $this->assertEquals('hello_world', Str::slug('hello_world', '_'));
    }


    public function testFinish()
    {
        $this->assertEquals('abbc', Str::finish('ab', 'bc'));
        $this->assertEquals('abbc', Str::finish('abbcbc', 'bc'));
        $this->assertEquals('abcbbc', Str::finish('abcbbcbc', 'bc'));
    }


    public function testIs()
    {
        $this->assertTrue(Str::is('/', '/'));
        $this->assertFalse(Str::is('/', ' /'));
        $this->assertFalse(Str::is('/', '/a'));
        $this->assertTrue(Str::is('foo/*', 'foo/bar/baz'));
        $this->assertTrue(Str::is('*/foo', 'blah/baz/foo'));
    }


    public function testSnake()
    {
        $this->assertEquals(
            "foo_bar_biz_bang",
            Str::snake("FooBarBizBang")
        );
    }


    public function testCamel()
    {
        $this->assertEquals(
            "fooBarBizBang",
            Str::camel("foo_bar_biz_bang")
        );
    }


    public function testStudly()
    {
        $this->assertEquals(
            "FooBarBizBang",
            Str::studly("foo_bar_biz_bang")
        );
    }


    public function testSingular()
    {
        $this->assertEquals(
            "snake",
            Str::singular("snakes")
        );
    }


    public function testUpper()
    {
        $this->assertEquals(
            "MARY HAD A LITTLE LAMB",
            Str::upper("Mary had a LITTLE Lamb")
        );
    }


    public function testLower()
    {
        $this->assertEquals(
            "mary had a little lamb",
            Str::lower("MARY HAD A LITTLE LAMB")
        );
    }


    public function testLimit()
    {
        $this->assertEquals(
            "mary had a little la...",
            Str::limit("mary had a little lamb", 20)
        );

        $this->assertEquals(
            "mary had a little lamb",
            Str::limit("mary had a little lamb")
        );
    }


    public function testQuickRandom()
    {
        $str = Str::quickRandom(16);

        $this->assertTrue(strlen($str) == 16, 'strlen is wrong');
        $this->assertTrue(is_string($str), 'string was expected');
    }


    public function testRandom()
    {
        $str = Str::random(16);

        $this->assertTrue(strlen($str) == 16, 'strlen is wrong');
        $this->assertTrue(is_string($str), 'string was expected');
    }


    public function testPlural()
    {
        $this->assertEquals("dogs", Str::Plural("dog"));
    }


    public function testLength()
    {
        $this->assertEquals(4, Str::length("test"));
    }
}
