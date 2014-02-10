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

namespace Trucker\Finders;

use Illuminate\Container\Container;
use Trucker\Facades\RequestFactory;
use Trucker\Facades\UrlGenerator;
use Trucker\Facades\Config;
use Trucker\Facades\ResponseInterpreterFactory;

/**
 * Class for finding model instances over the remote API
 *
 * @author Brian Webb <bwebb@indatus.com>
 */
class InstanceFinder
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new InstanceFinder
     *
     * @param Container $app
     * @param Client    $client
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * Function to find an instance of an Entity record
     *
     * @param  Trucker\Resource\Model $model       Model to use for URL generation etc.
     * @param  int           $id          The primary identifier value for the record
     * @param  array         $getParams   Array of GET parameters to pass
     * @return Trucker\Resource\Model              An instance of the entity requested
     */
    public function fetch($model, $id, $getParams = array())
    {
        $instance = null;

        //get a request object
        $request = RequestFactory::build();

        //init the request
        $request->createRequest(
            Config::get('base_uri'),
            UrlGenerator::getInstanceUri($model, [':id' => $id]),
            'GET'
        );


        //set any get parameters on the request
        $request->setGetParameters($getParams);

        //actually send the request
        $response = $request->sendRequest();


        
        if (! ResponseInterpreterFactory::build()->success($response)) {
            return null;
        }

        //kraft the response into an object to return
        $data     = $response->parseResponseToData();
        $klass    = $model->getResourceName();
        $instance = new $klass($data);

        //inflate the ID property that should be guarded
        $id = $instance->getIdentityProperty();
        if (array_key_exists($id, $data)) {
            $instance->{$id} = $data[$id];
        }

        return $instance;
    }
}
