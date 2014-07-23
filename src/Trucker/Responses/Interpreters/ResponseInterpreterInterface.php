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
use Trucker\Responses\BaseResponse;

/**
 * Interface to be implemented by classes that are 
 * deciphering API responses to determine the success
 * or failure of various requests
 */
interface ResponseInterpreterInterface
{
    /**
     * Constructor to setup the interpreter
     * 
     * @param Container $app      
     */
    public function __construct(Container $app);


    /**
     * Function to return a boolean value indicating wether
     * the request was successful or not 
     *
     * @param  $response - Trucker response to interpret
     * @return boolean
     */
    public function success(BaseResponse $response);


    /**
     * Function to return a boolean value indicating wether
     * the request indicated something was not found
     *
     * @param  $response - Trucker response to interpret
     * @return boolean
     */
    public function notFound(BaseResponse $response);


    /**
     * Function to return a boolean value indicating wether
     * the request was considered invalid
     *
     * @param  $response - Trucker response to interpret
     * @return boolean
     */
    public function invalid(BaseResponse $response);


    /**
     * Function to return a boolean value indicating wether
     * the request was ended in an error state
     *
     * @param  $response - Trucker response to interpret
     * @return boolean
     */
    public function error(BaseResponse $response);
}
