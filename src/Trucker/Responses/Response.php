<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Responses;

use Illuminate\Container\Container;
use Trucker\Facades\TransporterFactory;

class Response extends BaseResponse
{

    /**
     * The IoC Container
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Response object managed by this
     * class
     * 
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;

    /**
     * Build a new RequestManager
     *
     * @param Container $app
     * @param Client    $client
     */
    public function __construct(Container $app, \Guzzle\Http\Message\Response $response = null)
    {
        $this->app = $app;

        parent::__construct($response);
    }


    /**
     * Getter to access the IoC Container
     * 
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }



    /**
     * Magic function to pass methods not found
     * on this class down to the guzzle response
     * object that is being wrapped
     * 
     * @param  string $method name of called method
     * @param  array  $args   arguments to the method
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this, $method)) {
            return call_user_func_array(
                array($this->response, $method),
                $args
            );
        }
    // @codeCoverageIgnoreStart
    }// @codeCoverageIgnoreEnd


    /**
     * Create a new instance of the given model.
     *
     * @param  Container $app
     * @param  \Guzzle\Http\Message\Response $response
     * @return \Trucker\Responses\Response
     */
    public function newInstance(Container $app, \Guzzle\Http\Message\Response $response)
    {
    
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $r = new static($app, $response);
    
        return $r;
    }


    /**
     * Function to take a response object and convert it
     * into an array of data that is ready for use
     *
     * @return array           Parsed array of data
     */
    public function parseResponseToData()
    {
        $transporter = TransporterFactory::build();

        return $transporter->parseResponseToData($this->response);
    }


    /**
     * Function to take a response string (as a string) and depending on
     * the type of string it is, parse it into an object.
     *
     * @return object
     */
    public function parseResponseStringToObject()
    {
        $transporter = TransporterFactory::build();
        
        return $transporter->parseResponseStringToObject($this->response);
    }
}
