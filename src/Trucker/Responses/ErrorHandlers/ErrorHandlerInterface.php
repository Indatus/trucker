<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Responses\ErrorHandlers;

use Illuminate\Container\Container;

interface ErrorHandlerInterface
{
    /**
     * Constructor to setup the interpreter
     * 
     * @param Container $app      
     */
    public function __construct(Container $app);

    /**
     * Function to take the response object and return 
     * an array of errors
     * 
     * @param  Trucker\Responses\Response $response - response object
     * @return array - array of string error messages
     */
    public function parseErrors(\Trucker\Responses\Response $response);
}
