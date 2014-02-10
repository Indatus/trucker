<?php

namespace Trucker\Responses\ErrorHandlers;

use Illuminate\Container\Container;
use Trucker\Facades\Config;

class ParameterKeyErrorHandler implements ErrorHandlerInterface
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
     * Function to take the response object and return 
     * an array of errors
     * 
     * @param  Trucker\Responses\Response $response - response object
     * @return array - array of string error messages
     */
    public function parseErrors(\Trucker\Responses\Response $response)
    {
        $result    = $response->parseResponseStringToObject();
        $error_key = Config::get('errors_key');

        if (property_exists($result, $error_key)) {
            return $result->errors;
        } else {
            throw new \InvalidArgumentException("Error key [{$error_key}] does not exist in response");
        }
    }
}
