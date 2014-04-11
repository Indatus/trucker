<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Responses\Interpreters;

use Illuminate\Container\Container;
use Trucker\Facades\Config;

class HttpStatusCodeInterpreter implements ResponseInterpreterInterface
{
    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Constructor to setup the interpreter
     * 
     * @param Container $app      
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * Function to return a boolean value indicating whether
     * the request was successful or not 
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function success(\Trucker\Responses\Response $response)
    {
        return $this->matchesStatus('response.http_status.success', $response->getStatusCode());
    }


    /**
     * Function to return a boolean value indicating whether
     * the request indicated something was not found
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function notFound(\Trucker\Responses\Response $response)
    {
        return $this->matchesStatus('response.http_status.not_found', $response->getStatusCode());
    }


    /**
     * Function to return a boolean value indicating whether
     * the request was considered invalid
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function invalid(\Trucker\Responses\Response $response)
    {
        return $this->matchesStatus('response.http_status.invalid', $response->getStatusCode());
    }


    /**
     * Function to return a boolean value indicating whether
     * the request was ended in an error state
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function error(\Trucker\Responses\Response $response)
    {
        return $this->matchesStatus('response.http_status.error', $response->getStatusCode());
    }

    /**
     * Function to return a boolean value indicating whether
     * the provided status is matched by the configured setting.
     *
     * Currently supports:
     *
     * @param $option
     * @param $status
     *
     * @return bool
     */
    protected function matchesStatus($option, $status)
    {
        return $status == Config::get($option) || Config::contains($option, $status);
    }
}
