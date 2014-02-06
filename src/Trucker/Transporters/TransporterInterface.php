<?php

namespace Trucker\Transporters;

interface TransporterInterface
{

    /**
     * Function to set the appropriate headers on a request object
     * to facilitate a particular transport language
     * 
     * @param GuzzleHttpMessageRequest $request
     */
    public function setHeaderOnRequest(\Guzzle\Http\Message\Request &$request);

    /**
     * Function to convert a response object into an associative
     * array of data
     * 
     * @param  GuzzleHttpMessageResponse $response
     * @return array
     */
    public function parseResponseToData(\Guzzle\Http\Message\Response $response);

    /**
     * Function to parse the response string into an object
     * specific to the type of transport mechanism used i.e. json, xml etc
     * 
     * @param  GuzzleHttpMessageResponse $response
     * @return stdClass
     */
    public function parseResponseStringToObject(\Guzzle\Http\Message\Response $response);
}
