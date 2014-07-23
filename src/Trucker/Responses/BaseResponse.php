<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Responses;

/**
 * Provides a common type hint for responses.
 *
 * Classes that implement this interface both utilize the magic __call()
 * to delegate method calls to Guzzle.  Due to this magic, common methods
 * are not outlined in this interface to avoid conflicts/implementation.
 *
 * @method int getStatusCode
 */
class BaseResponse
{

    /**
     * Response object managed by this
     * class
     *
     * @var Response
     */
    protected $response;

    /**
     * Build a new RequestManager
     *
     * @param Response $response
     */
    public function __construct($response = null)
    {
        $this->response = $response;
    }

} 