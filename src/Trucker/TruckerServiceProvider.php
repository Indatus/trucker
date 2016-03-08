<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // done with boot()
        $this->app = static::make($this->app);
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

        //bind paths
        $app = $serviceProvider->bindPaths($app);

        // Bind classes
        $app = $serviceProvider->bindCoreClasses($app);
        $app = $serviceProvider->bindClasses($app);

        return $app;
    }

    /**
     * Bind the Trucker paths
     *
     * @param Container $app
     *
     * @return Container
     */
    public function bindPaths(Container $app)
    {
        $app->bind('trucker.bootstrapper', function ($app) {
            return new Bootstrapper($app);
        });

        // Bind paths
        $app['trucker.bootstrapper']->bindPaths();

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

            $fileloader = new FileLoader($app['files'], __DIR__ . '/../config');

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
            return new Url\UrlGenerator($app);
        });

        $app->singleton('trucker.config-manager', function ($app) {
            return new Support\ConfigManager($app);
        });

        $app->bind('trucker.instance-finder', function ($app) {
            return new Finders\InstanceFinder($app);
        });

        $app->bind('trucker.collection-finder', function ($app) {
            return new Finders\CollectionFinder($app);
        });

        $app->bind('trucker.response', function ($app) {
            return new Responses\Response($app);
        });

        $app->bind('trucker.model', function ($app) {
            return new Resource\Model();
        });

        //Factories
        $app->bind('trucker.conditions', function ($app) {
            return new Factories\QueryConditionFactory($app);
        });

        $app->bind('trucker.transporter', function ($app) {
            return new Factories\ApiTransporterFactory($app);
        });

        $app->bind('trucker.order', function ($app) {
            return new Factories\QueryResultOrderFactory($app);
        });

        $app->bind('trucker.interpreter', function ($app) {
            return new Factories\ResponseInterpreterFactory($app);
        });

        $app->bind('trucker.error-handler', function ($app) {
            return new Factories\ErrorHandlerFactory($app);
        });

        $app->bind('trucker.request-factory', function ($app) {
            return new Factories\RequestFactory($app);
        });

        $app->bind('trucker.auth', function ($app) {
            return new Factories\AuthFactory($app);
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
        $this->publishes([
            __DIR__ . '/../config/trucker.php' => $this->app['path.trucker.config'] . DIRECTORY_SEPARATOR . 'trucker.php'
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/trucker.php', 'trucker');

        return $app;
    }
}
