<?php

class Bootstrapper extends TruckerTests
{
    /**
     * The bootstrapper instance
     *
     * @var Bootstrapper
     */
    protected $bootstrapper;

    /**
     * Setup the tests
     */
    public function setUp()
    {
        parent::setUp();

        $this->bootstrapper = new Trucker\Bootstrapper($this->app);
        unset($this->app['path.base']);
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////////// TESTS /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    public function testDoesntRebindBasePath()
    {
        $base = 'src';
        $this->app->instance('path.base', $base);
        $this->bootstrapper->bindPaths();

        $this->assertEquals($base, $this->app['path.base']);
    }

    public function testCanBindBasePath()
    {
        $this->bootstrapper->bindPaths();

        $this->assertEquals(realpath(__DIR__.'/..'), $this->app['path.base']);
    }

    public function testCanBindConfigurationPaths()
    {
        $this->bootstrapper->bindPaths();

        $root = realpath(__DIR__.'/..');
        $this->assertEquals($root.'/.trucker', $this->app['path.trucker.config']);
    }

    public function testCanExportConfiguration()
    {
        $this->bootstrapper->bindPaths();
        $this->bootstrapper->exportConfiguration();

        $this->assertFileExists(__DIR__.'/../.trucker');
    }

    public function testCanReplaceStubsInConfigurationFile()
    {
        $this->bootstrapper->bindPaths();
        $path = $this->bootstrapper->exportConfiguration();
        $this->bootstrapper->updateConfiguration($path, array('basic_username' => 'foo'));

        $this->assertFileExists(__DIR__.'/../.trucker');
        $this->assertContains('foo', file_get_contents(__DIR__.'/../.trucker/auth.php'));
    }
}
