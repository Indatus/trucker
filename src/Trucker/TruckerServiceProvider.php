<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LICENSE: The BSD 3-Clause
 *
 * Copyright (c) 2013, Indatus
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list
 * of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other
 * materials provided with the distribution.
 *
 * Neither the name of Indatus nor the names of its contributors may be used
 * to endorse or promote products derived from this software without specific prior
 * written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT
 * OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Trucker
 * @author      Brian Webb <bwebb@indatus.com>
 * @copyright   2013 Indatus
 * @license     http://opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause
 */

namespace Trucker;

use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

 /**
  * Service Provider for interacting with the Trucker class
  *
  * @author Brian Webb <bwebb@indatus.com>
  */
class TruckerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register classes
        $this->app = static::make($this->app);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // done with boot()
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('trucker');
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////// CLASS BINDINGS /////////////////////////
    ////////////////////////////////////////////////////////////////////

    public static function make($app = null)
    {

        if (!$app) {
            $app = new Container;
        }

        $serviceProvider = new static($app);

        // Bind classes
        $app = $serviceProvider->bindCoreClasses($app);
        $app = $serviceProvider->bindClasses($app);

        return $app;
    }


    /**
     * Bind the core classes
     *
     * @param Container $app
     *
     * @return Container
     */
    public function bindCoreClasses(Container $app)
    {
        $app->bindIf('files', 'Illuminate\Filesystem\Filesystem');

        $app->bindIf('config', function ($app) {

            $fileloader = new FileLoader($app['files'], __DIR__.'/../config');

            return new Repository($fileloader, 'config');

        }, true);

        // Register factory and custom configurations
        $app = $this->registerConfig($app);

        return $app;
    }

    /**
     * Bind the ActiveResource classes to the Container
     *
     * @param Container $app
     *
     * @return Container
     */
    public function bindClasses(Container $app)
    {
        $app->singleton('trucker.urls', function ($app) {
            return new UrlGenerator($app);
        });

        $app->bind('trucker.request', function ($app) {
            return new Requests\RestRequest($app, new \Guzzle\Http\Client());
        });

        $app->bind('trucker.instance-finder', function ($app) {
            return new Finders\InstanceFinder($app);
        });

        $app->bind('trucker.collection-finder', function ($app) {
            return new Finders\CollectionFinder($app);
        });

        $app->bind('trucker.get-array-query', function ($app) {
            return new Finders\Conditions\GetArrayParamsQueryCondition($app);
        });

        $app->bind('trucker.get-array-result-order', function ($app) {
            return new Finders\Conditions\GetArrayParamsResultOrder($app);
        });

        $app->bind('trucker.response', function ($app) {
            return new Responses\Response($app);
        });
        
        $app->bind('trucker.model', function ($app) {
                return new Model();
        });

        return $app;
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////////// HELPERS ////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Register factory and custom configurations
     *
     * @param Container $app
     *
     * @return Container
     */
    protected function registerConfig(Container $app)
    {
        // Register config file
        $app['config']->package('indatus/trucker', __DIR__.'/../config');
        $app['config']->getLoader();


        // Register custom config
        $config_key = 'path.trucker.config';
        $custom = array_key_exists($config_key, $app) ? $app[$config_key] : null;
        if (file_exists($custom)) {
            $app['config']->afterLoading('trucker', function ($me, $group, $items) use ($custom) {
                $customItems = include $custom.'/'.$group.'.php';

                return array_replace_recursive($items, $customItems);
            });
        }

        return $app;
    }
}
