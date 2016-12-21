<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Requests;

use Guzzle\Http\Client;
use Illuminate\Container\Container;
use Trucker\Facades\Config;
use Trucker\Facades\ErrorHandlerFactory;
use Trucker\Facades\ResponseInterpreterFactory;
use Trucker\Facades\TransporterFactory;
use Trucker\Finders\Conditions\QueryConditionInterface;
use Trucker\Finders\Conditions\QueryResultOrderInterface;
use Trucker\Requests\Auth\AuthenticationInterface;
use Trucker\Resource\Model;
use Trucker\Responses\RawResponse;

class RestRequest implements RequestableInterface
{

    /**
     * The IoC Container
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Request client
     *
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * Request object managed by this
     * class
     *
     * @var \Guzzle\Http\Message\Request
     */
    protected $request;

    /**
     * Build a new RestRequest
     *
     * @param Container $app
     * @param Client    $client
     */
    public function __construct(Container $app, $client = null)
    {
        $this->app = $app;
        $this->client = $client == null ? new Client : $client;
    }

    /**
     * Getter function to access the HTTP Client
     *
     * @return \Guzzle\Http\Client
     */
    public function &getClient()
    {
        return $this->client;
    }

    /**
     * Function to create a Guzzle HTTP request
     *
     * @param  string $baseUri         The protocol + host
     * @param  string $path            The URI path after the host
     * @param  string $httpMethod      The HTTP method to use for the request (GET, PUT, POST, DELTE etc.)
     * @param  array  $requestHeaders  Any additional headers for the request
     * @param  string $httpMethodParam Post parameter to set with a string that
     *                                 contains the HTTP method type sent with a POST
     * @return RequestManager
     */
    public function createRequest($baseUri, $path, $httpMethod = 'GET', $requestHeaders = array(), $httpMethodParam = null)
    {
        $this->client->setBaseUrl($baseUri);

        if (!in_array(strtolower($httpMethod), array('get', 'put', 'post', 'patch', 'delete', 'head'))) {
            throw new Exception("Invalid HTTP method");
        }

        $method = strtolower($httpMethod);
        $method = $method == 'patch' ? 'put' : $method; //override patch calls with put

        if ($httpMethodParam != null && in_array($method, array('put', 'post', 'patch', 'delete'))) {
            $this->request = $this->client->post($path);
            $this->request->setPostField($httpMethodParam, strtoupper($method));
        } else {
            $this->request = $this->client->{$method}($path);
        }

        //set any additional headers on the request
        $this->setHeaders($requestHeaders);

        //setup how we get data back (xml, json etc)
        $this->setTransportLanguage();

        return $this->request;
    }

    /**
     * Function to set headers on the request
     *
     * @param  array  $requestHeaders  Any additional headers for the request
     * @return  void
     */
    public function setHeaders($requestHeaders = array())
    {
        foreach ($requestHeaders as $header => $value) {
            $this->request->setHeader($header, $value);
        }
    }

    /**
     * Function to set given file parameters
     * on the request
     *
     * @param array $params File parameters to set
     */
    public function setBody($body, $contentType = null)
    {
        if (method_exists($this->request, 'setBody')) {
            $this->request->setBody($body, $contentType);
        }
    }

    /**
     * Function to set POST parameters onto the request
     *
     * @param array $params Key value array of post params
     */
    public function setPostParameters($params = array())
    {
        foreach ($params as $key => $value) {
            $this->request->setPostField($key, $value);
        }
    }

    /**
     * Functio to set GET parameters onto the
     * request
     *
     * @param array $params Key value array of get params
     */
    public function setGetParameters($params = array())
    {
        $query = $this->request->getQuery();
        foreach ($params as $key => $val) {
            $query->add($key, $val);
        }
    }

    /**
     * Function to set given file parameters
     * on the request
     *
     * @param array $params File parameters to set
     */
    public function setFileParameters($params = array())
    {
        foreach ($params as $key => $value) {
            $this->request->addPostFile($key, $value);
        }
    }

    /**
     * Function to set the entities properties on the
     * request object taking into account any properties that
     * are read only etc.
     *
     * @param  \Trucker\Resource\Model
     */
    public function setModelProperties(Model $model)
    {
        $cantSet = $model->getReadOnlyFields();

        //set the property attributes
        foreach ($model->attributes() as $key => $value) {
            if (in_array($key, $model->getFileFields())) {
                $this->request->addPostFile($key, $value);
            } else {
                if (!in_array($key, $cantSet)) {
                    $this->request->setPostField($key, $value);
                }
            }
        }
    }

    /**
     * Function to set the language of data transport. Uses
     * TransporterFactory to pull a Transportable object
     * and set up the request
     *
     * @return  void
     */
    public function setTransportLanguage()
    {
        $transporter = TransporterFactory::build();
        $transporter->setHeaderOnRequest($this->request);
    }

    /**
     * Function to add an error handler to the request.  This could be used
     *
     * @param int     $httpStatus      HTTP status to error handle (-1 matches all)
     * @param Closure $func            Function to call on error
     * @param boolean $stopPropagation Boolean as to wether to stop event propagation
     */
    public function addErrorHandler($httpStatus, \Closure $func, $stopPropagation = true)
    {
        $request = $this->request;
        $this->request->getEventDispatcher()->addListener(
            'request.error',
            function (\Guzzle\Common\Event $event) use ($httpStatus, $stopPropagation, $func, $request) {
                if ($httpStatus == -1 || $event['response']->getStatusCode() == $httpStatus) {

                    // Stop other events from firing if needed
                    if ($stopPropagation) {
                        $event->stopPropagation();
                    }

                    //execute the callback
                    $func($event, $request);
                }
            }
        );
    }

    /**
     * Function to add Query conditions to the request
     *
     * @param QueryConditionInterface $condition condition to add to the request
     * @return  void
     */
    public function addQueryCondition(QueryConditionInterface $condition)
    {
        $condition->addToRequest($this->request);
    }

    /**
     * Function to add Query result ordering conditions to the request
     *
     * @param  QueryResultOrderInterface $resultOrder
     * @return void
     */
    public function addQueryResultOrder(QueryResultOrderInterface $resultOrder)
    {
        $resultOrder->addToRequest($this->request);
    }

    /**
     * Function to add authentication to the request
     *
     * @param  AuthenticationInterface $auth
     * @return void
     */
    public function authenticate(AuthenticationInterface $auth)
    {
        $auth->authenticateRequest($this->request);
    }

    /**
     * Function to send the request to the remote API
     *
     * @return  \Trucker\Responses\Response
     */
    public function sendRequest()
    {
        try {
            $response = $this->request->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $e) {
            $response = $e->getResponse();
        }

        return $this->app->make('trucker.response')->newInstance($this->app, $response);
    }

    /**
     * Function to execute a raw GET request
     *
     * @param  string $uri       uri to hit (i.e. /users)
     * @param  array  $params    Querystring parameters to send
     * @param  array  $headers   Optional headers to use
     * @return \Trucker\Responses\RawResponse
     */
    public function rawGet($uri, $params = array(), $headers = array())
    {
        return $this->rawRequest($uri, 'GET', array(), $params, array(), $headers);
    }

    /**
     * Function to execute a raw POST request
     *
     * @param  string $uri       uri to hit (i.e. /users)
     * @param  array  $params    POST parameters to send
     * @param  array  $getParams Querystring parameters to send
     * @param  array  $files     files to send (key = name, value = path)
     * @param  array  $headers   Optional headers to use
     * @return \Trucker\Responses\RawResponse
     */
    public function rawPost($uri, $params = array(), $getParams = array(), $files = array(), $headers = array())
    {
        return $this->rawRequest($uri, 'POST', $params, $getParams, $files, $headers);
    }

    /**
     * Function to execute a raw PUT request
     *
     * @param  string $uri       uri to hit (i.e. /users)
     * @param  array  $params    PUT parameters to send
     * @param  array  $getParams Querystring parameters to send
     * @param  array  $files     files to send (key = name, value = path)
     * @param  array  $headers   Optional headers to use
     * @return \Trucker\Responses\RawResponse
     */
    public function rawPut($uri, $params = array(), $getParams = array(), $files = array(), $headers = array())
    {
        return $this->rawRequest($uri, 'PUT', $params, $getParams, $files, $headers);
    }

    /**
     * Function to execute a raw PATCH request
     *
     * @param  string $uri       uri to hit (i.e. /users)
     * @param  array  $params    PATCH parameters to send
     * @param  array  $getParams Querystring parameters to send
     * @param  array  $files     files to send (key = name, value = path)
     * @param  array  $headers   Optional headers to use
     * @return \Trucker\Responses\RawResponse
     */
    public function rawPatch($uri, $params = array(), $getParams = array(), $files = array(), $headers = array())
    {
        return $this->rawRequest($uri, 'PATCH', $params, $getParams, $files, $headers);
    }

    /**
     * Function to execute a raw DELETE request
     *
     * @param  string $uri       uri to hit (i.e. /users)
     * @param  array  $params    Querystring parameters to send
     * @param  array  $headers   Optional headers to use
     * @return \Trucker\Responses\RawResponse
     */
    public function rawDelete($uri, $params = array(), $headers = array())
    {
        return $this->rawRequest($uri, 'DELETE', array(), $params, array(), $headers);
    }

    /**
     * Function to execute a raw request on the base URI with the given uri path
     * and params
     *
     * @param  string $uri       uri to hit (i.e. /users)
     * @param  string $method    Request method (GET, PUT, POST, PATCH, DELETE, etc.)
     * @param  array  $params    PUT or POST parameters to send
     * @param  array  $getParams Querystring parameters to send
     * @param  array  $files     PUT or POST files to send (key = name, value = path)
     * @param  array  $headers   Optional headers to use
     * @return \Trucker\Responses\RawResponse
     */
    public function rawRequest($uri, $method, $params = array(), $getParams = array(), $files = array(), $headers = array())
    {
        $this->request = self::createRequest(
            Config::get('request.base_uri'),
            $uri,
            $method
        );

        $this->setPostParameters($params);
        $this->setGetParameters($getParams);
        $this->setFileParameters($files);
        $this->setHeaders($headers);

        //encode the request body
        /** @var \Trucker\Transporters\TransporterInterface $transporter */
        $transporter = TransporterFactory::build();
        $transporter->setRequestBody($this, $params);

        // Trucker\Response
        $response = $this->sendRequest();

        //handle clean response with errors
        if (ResponseInterpreterFactory::build()->invalid($response)) {

            //get the errors and set them to our local collection
            $errors = (array) ErrorHandlerFactory::build()->parseErrors($response);

            return new RawResponse(false, $response, $errors);

        } //end if

        return new RawResponse(true, $response);

    } //end rawRequest
}
