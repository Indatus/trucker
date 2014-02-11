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
use Trucker\Facades\AuthFactory;
use Trucker\Responses\Collection;
use Trucker\Finders\Conditions\QueryConditionInterface;
use Trucker\Finders\Conditions\QueryResultOrderInterface;
use Trucker\Resource\Model;

/**
 * Class for finding collections of models over the remote API
 *
 * @author Brian Webb <bwebb@indatus.com>
 */
class CollectionFinder
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new CollectionFinder
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * Function to fetch a collection of Trucker\Resource\Model object
     * from the remote API.
     * 
     * @param  Model                      $model       Instance of entity type being fetched
     * @param  QueryConditionInterface    $condition   Query conditions for the request
     * @param  QueryResultOrderInterface  $resultOrder Result ordering requirements for the request
     * @param  array                      $getParams   Additional GET parameters to send w/ request
     * @return Trucker\Responses\Collection
     */
    public function fetch(
        Model $model,
        QueryConditionInterface $condition = null,
        QueryResultOrderInterface $resultOrder = null,
        array $getParams = []
    ) {

        //get a request object
        $request = RequestFactory::build();
        
        //init the request
        $request->createRequest(
            Config::get('base_uri'),
            UrlGenerator::getCollectionUri($model),
            'GET'
        );

        //add auth if it is needed
        if ($auth = AuthFactory::build()) {
            $request->authenticate($auth);
        }

        //add query conditions if needed
        if ($condition) {
            $request->addQueryCondition($condition);
        }

        //add result ordering if needed
        if ($resultOrder) {
            $request->addQueryResultOrder($resultOrder);
        }

        //set any get parameters on the request
        $request->setGetParameters($getParams);

        //actually send the request
        $response = $request->sendRequest();

        //get api response
        $data = $response->parseResponseToData();

        //make an array to hold results
        $records = array();

        //figure out wether a collection key is used
        $collection_key = Config::get('collection_key');

        //set records array appropriatley
        if (isset($collection_key)) {
            $recordCollection = $data[$collection_key];
        } else {
            $recordCollection = $data;
        }

        //create an array of popuplated results
        foreach ($recordCollection as $values) {
            $klass     = $model->getResourceName();
            $instance = new $klass($values);

            //inflate the ID property that should be guarded
            $id = $instance->getIdentityProperty();
            if (array_key_exists($id, $values)) {
                $instance->{$id} = $values[$id];
            }

            //add the instance to the records array
            $records[] = $instance;

        }//end foreach

        //create a collection object to return
        $collection = new Collection($records);

        // if there was a collection_key, put any extra data that was returned
        // outside the collection key in the metaData attribute
        if (isset($collection_key)) {
            $collection->metaData = array_diff_key($data, array_flip((array) array($collection_key)));
        }

        return $collection;
    }
}
