<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Requests\Auth;

use Illuminate\Container\Container;
use Trucker\Facades\Config;
use Trucker\Requests\Auth\AuthenticationInterface;

class BasicAuthenticator implements AuthenticationInterface
{
    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;

    /**
     * Constructor, likely never called in implementation
     * but rather through the Factory
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Function to add the necessary authentication
     * to the request
     *
     * @param Guzzle\Http\Message\Request $request Request passed by reference
     * @return  void
     */
    public function authenticateRequest(&$request)
    {
        $username = Config::get('auth.basic.username');
        $password = Config::get('auth.basic.password');
        $request->setAuth($username, $password);
    }
}
