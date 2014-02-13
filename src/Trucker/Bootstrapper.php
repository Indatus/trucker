<?php
/*
 * This file is derived largley from the Igniter class
 * in the Rocketeer package which is licensed under MIT
 * and (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * @see https://github.com/Anahkiasen/rocketeer/blob/master/LICENSE
 */
namespace Trucker;

use Illuminate\Container\Container;

/**
 * Finds configurations and paths
 *
 * @author Maxime Fabre <ehtnam6@gmail.com>
 * @author Brian Webb <bwebb@indatus.com>
 */
class Bootstrapper
{
    /**
     * The Container
     *
     * @var Container
     */
    protected $container;

    /**
     * Build a new Bootstrapper
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Bind paths to the container
     *
     * @return void
     */
    public function bindPaths()
    {
        $this->bindBase();
        $this->bindConfiguration();
    }

    ////////////////////////////////////////////////////////////////////////
    /////////////////////////////// BOOTSTRAPPIN ///////////////////////////
    ////////////////////////////////////////////////////////////////////////

    /**
     * Get the path to the configuration folder
     *
     * @return string
     */
    public function getConfigurationPath()
    {
        // Return path to Laravel configuration
        if ($this->app->bound('path')) {
            $laravel = $this->app['path'].'/config/packages/indatus/trucker';
            if (file_exists($laravel)) {
                return $laravel;
            }
        }

        return $this->app['path.trucker.config'];
    }

    /**
     * Export the configuration files
     *
     * @return void
     */
    public function exportConfiguration()
    {
        $source      = __DIR__.'/../config';
        $destination = $this->getConfigurationPath();

        // Unzip configuration files
        $this->app['files']->copyDirectory($source, $destination);

        return $destination;
    }

    /**
     * Replace placeholders in configuration
     *
     * @param string $folder
     * @param array  $values
     *
     * @return void
     */
    public function updateConfiguration($folder, array $values = array())
    {
        // Replace stub values in files
        $files = $this->app['files']->files($folder);
        foreach ($files as $file) {
            foreach ($values as $name => $value) {
                $contents = str_replace('{' .$name. '}', $value, file_get_contents($file));
                $this->app['files']->put($file, $contents);
            }
        }
    }

    ////////////////////////////////////////////////////////////////////
    //////////////////////////////// PATHS /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Bind the base path to the Container
     *
     * @return void
     */
    protected function bindBase()
    {
        if ($this->app->bound('path.base')) {
            return;
        }

        $this->app->instance('path.base', getcwd());
    }

    /**
     * Bind paths to the configuration files
     *
     * @return void
     */
    protected function bindConfiguration()
    {
        $path = $this->app['path.base'] ? $this->app['path.base'].'/' : '';
        $logs = $this->app->bound('path.storage') ? str_replace($this->unifySlashes($path), null, $this->unifySlashes($this->app['path.storage'])) : '.trucker';

        $paths = array(
            'config' => '.trucker',
            'logs'   => $logs.'/logs',
        );

        foreach ($paths as $key => $file) {
            $filename = $path.$file;

            // Check whether we provided a file or folder
            if (!is_dir($filename) and file_exists($filename.'.php')) {
                $filename .= '.php';
            }

            // Use configuration in current folder if none found
            $realpath = realpath('.').'/'.$file;
            if (!file_exists($filename) and file_exists($realpath)) {
                $filename = $realpath;
            }

            $this->app->instance('path.trucker.'.$key, $filename);
        }
    }
    
    /**
     * Unify the slashes to the UNIX mode (forward slashes)
     * @param  string $path
     * @return string
     */
    protected function unifySlashes($path)
    {
        return str_replace('\\', '/', $path);
    }
}