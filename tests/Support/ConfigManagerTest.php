<?php

use Trucker\Support\ConfigManager;
use Mockery as m;

class ConfigManagerTest extends TruckerTests
{

    public function testGetApp()
    {
        $cm = new ConfigManager($this->app);
        $this->assertEquals($this->app, $cm->getApp());
    }

    
    public function testSetApp()
    {
        $mApp = m::mock('Illuminate\Container\Container');
        $cm = new ConfigManager($this->app);
        $cm->setApp($mApp);
        $this->assertEquals($mApp, $cm->getApp());
    }

    public function testGet()
    {
        $cm = new ConfigManager($this->app);
        $this->assertEquals('rest', $cm->get('request.driver'));
    }

    public function testSet()
    {

        $app = new Illuminate\Container\Container;
        $config = m::mock('Illuminate\Config\Repository');

        $config->shouldReceive('set')
            ->once()
            ->with('trucker::request.driver', 'foo');

        $config->shouldReceive('get')
            ->once()
            ->with('trucker::request.driver')
            ->andReturn('foo');

        $app['config']  = $config;

        $cm = new ConfigManager($app);
        $cm->set('request.driver', 'foo');
        $this->assertEquals('foo', $cm->get('request.driver'));
    }

    public function testContains()
    {
        $this->swapConfig([
                'trucker::response.http_status.success' => [200, 201]
            ]);
        $cm = new ConfigManager($this->app);
        $this->assertTrue($cm->contains('response.http_status.success', 200));
    }
}
