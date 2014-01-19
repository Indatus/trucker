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

namespace Trucker\Responses;

/**
 * Result class returned from Trucker when
 * a raw request is initiated
 *
 * @author Brian Webb <bwebb@indatus.com>
 */
class RawResponse
{
    /**
     * Response object
     *
     * @var Trucker\Response
     */
    private $response = null;

    /**
     * Var to hold any errors returned
     * 
     * @var array
     */
    private $errors = array();

    /**
     * Var to tell if the request was successful
     * 
     * @var boolean
     */
    public $success = false;

    /**
     * Constructor
     * 
     * @param boolean $successful 
     * @param Object  $response  
     * @param array   $errors     
     */
    public function __construct($successful = false, $response = null, $errors = array())
    {
        $this->success = $successful;
        $this->response = $response;
        $this->errors = $errors;
    }

    /**
     * Getter for errors
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Getter for response
     */
    public function response()
    {
        return $this->response->parseResponseStringToObject();
    }

    public function getResponse()
    {
        return $this->response;
    }
}
