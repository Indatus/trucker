<?php

namespace Trucker\Requests;

use Illuminate\Container\Container;
use Trucker\Finders\Conditions\QueryConditionInterface;
use Trucker\Finders\Conditions\QueryResultOrderInterface;
use Trucker\Model;

/**
 * Interface to dictate management of query conditions for a request
 */
interface RequestableInterface
{

    public function __construct(Container $app, $client = null);

    public function getApp();

    public function setApp($app);

    public function &getClient();

    public function getOption($option);

    public function createRequest($baseUri, $path, $httpMethod = 'GET', $requestHeaders = array(), $httpMethodParam = null);

    public function setBasicAuth($username, $password);

    public function setHeaders($requestHeaders = array());

    public function setPostParameters($params = array());

    public function setGetParameters($params = array());

    public function setFileParameters($params = array());

    public function setModelProperties(Model $model);

    public function setTransportLanguage($transporter);

    public function addErrorHandler($httpStatus, \Closure $func, $stopPropagation = true);

    public function addQueryCondition(QueryConditionInterface $condition);

    public function addQueryResultOrder(QueryResultOrderInterface $resultOrder);

    public function sendRequest($debug = false);
}
