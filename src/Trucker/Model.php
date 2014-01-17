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

namespace Trucker;

use Trucker\Facades\RequestManager;

/**
 * Base class for interacting with a remote API.
 *
 * @author Brian Webb <bwebb@indatus.com>
 */
class Model
{
    /**
     * The Trucker version
     *
     * @var string
     */
    const VERSION = '1.0.0';

    /**
     * The IoC Container
     *
     * @var Container
     */
    protected $app;

    /**
     * Post parameter to set with a string that
     * contains the HTTP method type sent with a POST
     * request rather than sending the true method.
     *
     * @var string
     */
    protected $httpMethodParam = null;

    /**
     * Protocol + host of base URI to remote API
     * i.e. http://example.com
     *
     * @var string
     */
    protected $baseUri;

    /**
     * Property to overwrite the getResourceName()
     * function with a static value
     *
     * @var string
     */
    protected $resourceName;

    /**
     * Property to overwrite the getURI()
     * function with a static value of what remote API URI path
     * to hit
     *
     * @var string
     */
    protected $uri;

    /**
     * Property to hold the data about entities for which this
     * resource is nested beneath.  For example if this entity was
     * 'Employee' which was a nested resource under a 'Company' and
     * the instance URI should be /companies/:company_id/employees/:id
     * then you would assign this string with 'Company:company_id'.
     * Doing this will allow you to pass in ':company_id' as an option
     * to the URI creation functions and ':company_id' will be replaced
     * with the value passed.
     *
     * Alternativley you could set the value to something like 'Company:100'.
     * You could do this before a call like:
     *
     * <code>
     * Employee::nestedUnder('Company:100');
     * $e = Employee::find(1);
     * </code>
     *
     * <code>
     * //this would hit /companies/100/employees/1
     * </code>
     *
     * @var string
     */
    protected $nestedUnder;

    /**
     * Username for remote API authentication if required
     *
     * @var string
     */
    protected $authUser;

    /**
     * Password for remote API authentication if required
     * @var string
     */
    protected $authPass;

    /**
     * Transport method of data from remote API
     * @var string
     */
    protected $transporter;

    /**
     * Array of instance values
     * @var array
     */
    protected $properties = array();

    /**
     * Element name that should contain a collection in a
     * response where more than one result is returned
     *
     * @var string
     */
    protected $collectionKey;

    /**
     * Name of the parameter key used to contain search
     * rules for fetching collections
     *
     * @var string
     */
    protected $searchParameter;

    /**
     * Name of the parameter key used to identify
     * an entity attribute
     *
     * @var string
     */
    protected $searchProperty;

    /**
     * Name of the parameter key used to specify
     * a search rule operator i.e.: = >= <= != LIKE
     *
     * @var string
     */
    protected $searchOperator;

    /**
     * Name of the parameter key used to identify
     * an entity value when searching
     * @var string
     */
    protected $searchValue;

    /**
     * Name of the parameter key used to identify
     * how search criteria should be joined
     *
     * @var string
     */
    protected $logicalOperator;

    /**
     * Name of the parameter key used to identify
     * the property to order search results by
     *
     * @var string
     */
    protected $orderBy;

    /**
     * Name of the parameter key used to identify
     * the order direction of search results
     *
     * @var string
     */
    protected $orderDir;

    /**
     * Name of the parameter value for specifying
     * "AND" search rule combination behavior
     *
     * @var string
     */
    protected $searchOperatorAnd;

    /**
     * Name of the parameter value for specifying
     * "OR" search rule combination behavior
     *
     * @var string
     */
    protected $searchOperatorOr;

    /**
     * Name of the parameter value for specifying
     * ascending result ordering
     *
     * @var string
     */
    protected $orderDirAsc;

    /**
     * Name of the parameter value for specifying
     * descending result ordering
     *
     * @var string
     */
    protected $orderDirDesc;

    /**
     * Remote resource's primary key property
     *
     * @var string
     */
    protected $identityProperty;

    /**
     * Var to hold instance errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Comma separated list of properties that can't
     * be set via mass assignment
     *
     * @var string
     */
    protected $guarded = "";

    /**
     * Comma separated list of properties that will take
     * a file path that should be read in and sent
     * with any API request
     *
     * @var string
     */
    protected $fileFields = "";

    /**
     * Comma separated list of properties that may be in
     * a GET request but should not be added to a create or
     * update request
     *
     * @var string
     */
    protected $readOnlyFields = "";

    /**
     * Array of files that were temporarily written for a request
     * that should be removed after the request is done.
     *
     * @var array
     */
    private $postRequestCleanUp = array();

    /**
     * Filesystem location that temporary files could be
     * written to if needed
     *
     * @var string
     */
    protected $scratchDiskLocation;




    /**
     * Constructor used to popuplate the instance with
     * attribute values
     *
     * @param array $attributes Associative array of property names and values
     */
    public function __construct($attributes = array())
    {

        $this->baseUri             = RequestManager::getOption('base_uri');
        $this->httpMethodParam     = RequestManager::getOption('http_method_param');
        $this->scratchDiskLocation = RequestManager::getOption('scratch_disk_location');
        $this->transporter         = RequestManager::getOption('transporter');
        $this->identityProperty    = RequestManager::getOption('identity_property');
        $this->collectionKey       = RequestManager::getOption('collection_key');
        $this->searchParameter     = RequestManager::getOption('search.container_parameter');
        $this->searchProperty      = RequestManager::getOption('search.property');
        $this->searchOperator      = RequestManager::getOption('search.operator');
        $this->searchValue         = RequestManager::getOption('search.value');
        $this->logicalOperator     = RequestManager::getOption('search.logical_operator');
        $this->orderBy             = RequestManager::getOption('search.order_by');
        $this->orderDir            = RequestManager::getOption('search.order_dir');
        $this->searchOperatorAnd   = RequestManager::getOption('search.and_operator');
        $this->searchOperatorOr    = RequestManager::getOption('search.or_operator');
        $this->orderDirAsc         = RequestManager::getOption('search.order_dir_ascending');
        $this->orderDirDesc        = RequestManager::getOption('search.order_dir_descending');

        //$this->inflateFromArray($attributes);
    }


    /**
     * Set the IoC container
     * 
     * @param Container
     */
    public function setApp($app)
    {
        $this->app = $app;
    }


    /**
     * Magic getter function for accessing instance properties
     *
     * @param  string $key  Property name
     * @return any          The value stored in the property
     */
    public function __get($key)
    {
        if ($key === 'attributes') {
            return $this->properties;
        }

        if (array_key_exists($key, $this->properties)) {
            return $this->properties[$key];
        }

        return null;
    }


    /**
     * Magic setter function for setting instance properties
     *
     * @param   string    $property   Property name
     * @param   any       $value      The value to store for the property
     * @return  void
     */
    public function __set($property, $value)
    {
        //if property contains '_base64'
        if (!(stripos($property, '_base64') === false)) {

            //if the property IS a file field
            $fileProperty = str_replace('_base64', '', $property);
            if (in_array($fileProperty, self::getFileFields())) {
                $this->handleBase64File($fileProperty, $value);
            }//end if file field

        } else {

            $this->properties[$property] = $value;
        }

    }//end __set


    /**
     * Magic unsetter function for unsetting an instance property
     * 
     * @param string $property Property name
     * @return void
     */
    public function __unset($property)
    {
        if (array_key_exists($property, $this->properties)) {
            unset($this->properties[$property]);
        }
    }//end __unset


    public function attributes()
    {
        return $this->properties;
    }
}
