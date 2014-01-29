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

use Trucker\Facades\Request;
use Trucker\Facades\Instance;
use Trucker\Facades\Collection;
use Trucker\Finders\Conditions\QueryConditionInterface;
use Trucker\Finders\Conditions\QueryResultOrderInterface;

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
    public $nestedUnder;

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
     * Element name that should contain 1+ errors
     * in a response that was invalid.
     *
     * @var string
     */
    protected $errorsKey;

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
     * The HTTP response status code that
     * will accompany a successful API response
     * 
     * @var integer
     */
    protected $httpStatusSuccess;

    /**
     * The HTTP response status code that
     * will accompany a not-found API response
     * 
     * @var integer
     */
    protected $httpStatusNotFound;

    /**
     * The HTTP response status code that
     * will accompany an unsuccessful API response
     * such as an entity could not be saved
     * 
     * @var integer
     */
    protected $httpStatusInvalid;

    /**
     * The HTTP response status code that
     * will accompany an error encountered  while
     * returning an API response
     * 
     * @var integer
     */
    protected $httpStatusError;

    /**
     * Portion of a property name that would indicate
     * that the value would be Base64 encoded when the 
     * property is set.
     * 
     * @var string
     */
    protected $base64Indicator;




    /**
     * Constructor used to popuplate the instance with
     * attribute values
     *
     * @param array $attributes Associative array of property names and values
     */
    public function __construct($attributes = [])
    {

        $initFromConfig = [
            'baseUri'             => 'base_uri',
            'httpMethodParam'     => 'http_method_param',
            'scratchDiskLocation' => 'scratch_disk_location',
            'transporter'         => 'transporter',
            'identityProperty'    => 'identity_property',
            'collectionKey'       => 'collection_key',
            'errorsKey'           => 'errors_key',
            'searchParameter'     => 'search.container_parameter',
            'searchProperty'      => 'search.property',
            'searchOperator'      => 'search.operator',
            'searchValue'         => 'search.value',
            'logicalOperator'     => 'search.logical_operator',
            'orderBy'             => 'search.order_by',
            'orderDir'            => 'search.order_dir',
            'searchOperatorAnd'   => 'search.and_operator',
            'searchOperatorOr'    => 'search.or_operator',
            'orderDirAsc'         => 'search.order_dir_ascending',
            'orderDirDesc'        => 'search.order_dir_descending',
            'httpStatusSuccess'   => 'http_status.success',
            'httpStatusNotFound'  => 'http_status.not_found',
            'httpStatusInvalid'   => 'http_status.invalid',
            'base64Indicator'     => 'base_64_property_indication',
        ];

        foreach ($initFromConfig as $property => $config_key) {
            $this->{$property} = Request::getOption($config_key);
        }

        $this->fill($attributes);
    }


    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @return \Trucker\Model
     */
    public function newInstance($attributes = [])
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects.
        $model = new static;

        $model->fill((array) $attributes);
    
        return $model;
    }
    


    /**
     * Magic getter function for accessing instance properties
     *
     * @param  string $key  Property name
     * @return any          The value stored in the property
     */
    public function __get($key)
    {
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
        if (!(stripos($property, $this->base64Indicator) === false)) {

            //if the property IS a file field
            $fileProperty = str_replace($this->base64Indicator, '', $property);
            if (in_array($fileProperty, $this->getFileFields())) {
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


    /**
     * Getter function to access the
     * underlying attributes array for the
     * entity
     * 
     * @return arrayhttpStatusError
     */
    public function attributes()
    {
        return $this->properties;
    }


    /**
     * Function to return any errors that
     * may have prevented a save
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }


    /**
     * Function to fill an instance's properties from an
     * array of keys and values
     *
     * @param  array  $attributes   Associative array of properties and values
     * @return void
     */
    public function fill($attributes = [])
    {
        $guarded = $this->getGuardedAttributes();

        foreach ($attributes as $property => $value) {
            if (!in_array($property, $guarded)) {

                //get the fields on the entity that are files
                $fileFields = $this->getFileFields();

                //if property contains base64 indicator
                if (!(stripos($property, $this->base64Indicator) === false)) {

                    //if the property IS a file field
                    $fileProperty = str_replace($this->base64Indicator, '', $property);

                    if (in_array($fileProperty, $fileFields)) {

                        $this->handleBase64File($fileProperty, $value);

                    }//end if file field

                } else {

                    //handle as normal property, but file fields can't be mass assigned
                    if (!in_array($property, $fileFields)) {

                        $this->properties[$property] = $value;

                    }
                }//end if-else base64
            }//end if not guarded
        }//end foreach
    }


    /**
     * Function to return an array of properties that should not
     * be set via mass assignment
     *
     * @return array
     */
    public function getGuardedAttributes()
    {
        $attrs = array_map('trim', explode(',', $this->guarded));

        //the identityProperty should always be guarded
        if (!in_array($this->identityProperty, $attrs)) {
            $attrs[] = $this->identityProperty;
        }

        return $attrs;
    }


    /**
     * Function to return an array of properties that will
     * accept a file path
     *
     * @return array
     */
    public function getFileFields()
    {
        $attrs = array_map('trim', explode(',', $this->fileFields));
        return array_filter($attrs);
    }


    /**
     * Function to take base64 encoded image and write it to a
     * temp file, then add that file to the property list to get
     * added to a request.
     *
     * @param  string $property Entity attribute
     * @param  string $value    Base64 encoded string
     * @return void
     */
    protected function handleBase64File($property, $value)
    {
        $image = base64_decode($value);
        $imgData = getimagesizefromstring($image);
        $mimeExp = explode("/", $imgData['mime']);
        $ext = end($mimeExp);
        $output_file = implode(
            DIRECTORY_SEPARATOR,
            array($this->scratchDiskLocation, uniqid("tmp_{$property}_").".$ext")
        );
        $f = fopen($output_file, "wb");
        fwrite($f, $image);
        fclose($f);

        $this->postRequestCleanUp[] = $output_file;
        $this->{$property} = $output_file;

    }//end handleBase64File


    /**
     * Function to get the instance ID, returns false if there
     * is not one
     *
     * @return instanceId | false
     */
    public function getId()
    {
        if (array_key_exists($this->identityProperty, $this->properties)) {
            return $this->properties[$this->identityProperty];
        }

        return false;
    }


    /**
     * Getter function to return the identity property
     * 
     * @return string
     */
    public function getIdentityProperty()
    {
        return $this->identityProperty;
    }


    /**
     * Function to return an array of property names
     * that are read only
     * 
     * @return array
     */
    public function getReadOnlyFields()
    {
        $cantSet = array_map('trim', explode(',', $this->readOnlyFields));
        return $cantSet;
    }


    /**
     * Function to get an associative array of fields
     * with their values that are NOT read only
     * 
     * @return array
     */
    public function getMutableFields()
    {
        $cantSet = $this->getReadOnlyFields();

        $mutableFields = array();

        //set the property attributes
        foreach ($this->properties as $key => $value) {
            if (!in_array($key, $cantSet)) {
                $mutableFields[$key] = $value;
            }
        }

        return $mutableFields;
    }


    /**
     * Function to interpret the URI resource name based on the class called.
     * Generally this would be the name of the class.
     *
     * @return string   The sub name of the resource
     */
    public function getResourceName()
    {
        if (isset($this->resourceName)) {
            return $this->resourceName;
        }

        $full_class_arr = explode("\\", get_called_class());
        $klass = end($full_class_arr);
        $this->resourceName = $klass;

        return $klass;
    }


    /**
     * Getter function to return a URI
     * that has been manually set
     * 
     * @return string
     */
    public function getURI()
    {
        return $this->uri ?: null;
    }


    /**
     * Function to find an instance of an Entity record
     *
     * @param  int           $id          The primary identifier value for the record
     * @param  array         $getParams   Array of GET parameters to pass
     * @return Trucker\Model              An instance of the entity requested
     */
    public static function find($id, $getParams = [])
    {
        return Instance::fetch(new static, $id, $getParams);
    }


    /**
     * Function to find a collection of Entity records from the remote api
     * 
     * @param  QueryConditionInterface    $condition   query conditions
     * @param  QueryResultOrderInterface  $resultOrder result ordering info
     * @param  array                      $getParams   additional GET params
     * @return Trucker\Responses\Collection
     */
    public static function all(
        QueryConditionInterface $condition = null,
        QueryResultOrderInterface $resultOrder = null,
        array $getParams = []
    ) {
        return Collection::fetch(new static, $condition, $resultOrder, $getParams);
    }


    /**
     * Function to handle persistance of the entity across the
     * remote API.  Function will handle either a CREATE or UPDATE
     *
     * @return Boolean  Success of the save operation
     */
    public function save()
    {
        if ($this->getId() === false) {

            //make a CREATE request
            Request::createRequest(
                Request::getOption('base_uri'),
                UrlGenerator::getCreateUri(self),
                'POST'
            );

        } else {

            //make an UPDATE request
            Request::createRequest(
                Request::getOption('base_uri'),
                UrlGenerator::getDeleteUri(
                    self,
                    [':'.$this->getIdentityProperty() => $this->getId()]
                ),
                'PATCH'
            );
        }

        //handle error saving & any errors given
        Request::addErrorHandler(
            422,
            function ($event, $request) {
                $response = Response::newInstance(Request::getApp(), $event['response']);
                $parsed = $response->parseResponseStringToObject();

                if (property_exists($parsed, Request::getOption('errors_key'))) {
                    $this->errors = $parsed->errors;
                }

                //return false, create failed
                $this->doPostRequestCleanUp();
                return false;
            },
            true
        );


        //set the property attributes on the request
        Request::setModelProperties($this);

        //actually send the request
        $response = Request::sendRequest();

        //handle clean response with errors
        if ($response->getStatusCode() == 422) {

            //get the errors and set them
            $result = $response->parseResponseStringToObject();
            if (property_exists($result, Request::getOption('errors_key'))) {
                $this->errors = $result->errors;
            }
            $this->doPostRequestCleanUp();

            return false;
        }//end if

        //get the response and inflate from that
        $data = $response->parseResponseToData();
        $this->fill($data);

        $this->doPostRequestCleanUp();
        return true;
    }


    /**
     * Function to delete an existing entity
     *
     * @return Boolean  Success of the delete operation
     */
    public function destroy()
    {

        //init the request
        Request::createRequest(
            Request::getOption('base_uri'),
            UrlGenerator::getDeleteUri(
                self,
                [':'.$this->getIdentityProperty() => $this->getId()]
            ),
            'DELETE'
        );

        //actually send the request
        $response = Request::sendRequest();

        $this->doPostRequestCleanUp();

        if ($response->getStatusCode() == 200) {
            return true;
        }

        return false;
    }


    /**
     * Function to clean up any temp files written for a request
     *
     * @return void
     */
    protected function doPostRequestCleanUp()
    {
        while (count($this->postRequestCleanUp) > 0) {
            $f = array_pop($this->postRequestCleanUp);
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }
}
