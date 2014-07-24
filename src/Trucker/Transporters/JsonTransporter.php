<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Transporters;

use Trucker\Requests\RestRequest;

class JsonTransporter implements TransporterInterface
{

    /**
     * Function to set the appropriate headers on a request object
     * to facilitate a JSON transport
     *
     * @param GuzzleHttpMessageRequest $request
     */
    public function setHeaderOnRequest(\Guzzle\Http\Message\Request &$request)
    {
        $request->setHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Function to convert a response object into an associative
     * array of data
     * 
     * @param  GuzzleHttpMessageResponse $response
     * @return array
     */
    public function parseResponseToData(\Guzzle\Http\Message\Response $response)
    {
        return $response->json();
    }

    /**
     * Function to parse the response string into an object
     * specific to JSON
     * 
     * @param  GuzzleHttpMessageResponse $response
     * @return stdClass
     */
    public function parseResponseStringToObject(\Guzzle\Http\Message\Response $response)
    {
        return json_decode($response->getBody(true));
    }

    /**
     * Set the request body for the given request.
     *
     * @param RestRequest $request
     * @param             $body
     */
    public function setRequestBody(RestRequest &$request, $body)
    {
        $request->setBody(json_encode($body), 'application/json');
    }
}
