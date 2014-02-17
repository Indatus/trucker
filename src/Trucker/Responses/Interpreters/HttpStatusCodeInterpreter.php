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
     * Function to return a boolean value indicating wether
     * the request was successful or not 
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function success(\Trucker\Responses\Response $response)
    {
        return $response->getStatusCode() == Config::get('response.http_status.success');
    }


    /**
     * Function to return a boolean value indicating wether
     * the request indicated something was not found
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function notFound(\Trucker\Responses\Response $response)
    {
        return $response->getStatusCode() == Config::get('response.http_status.not_found');
    }


    /**
     * Function to return a boolean value indicating wether
     * the request was considered invalid
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function invalid(\Trucker\Responses\Response $response)
    {
        return $response->getStatusCode() == Config::get('response.http_status.invalid');
    }


    /**
     * Function to return a boolean value indicating wether
     * the request was ended in an error state
     *
     * @param  $response - Guzzle response to interpret
     * @return boolean
     */
    public function error(\Trucker\Responses\Response $response)
    {
        return $response->getStatusCode() == Config::get('response.http_status.error');
    }
}
