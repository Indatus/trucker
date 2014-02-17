<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Finders\Conditions;

use Illuminate\Container\Container;

/**
 * Interface to dictate management of how results should be ordered
 * on a remote API request
 */
interface QueryResultOrderInterface
{

    /**
     * Constructor, likely never called in implementation
     * but rather through the service provider
     * 
     * @param Container $app
     */
    public function __construct(Container $app);


    /**
     * Function to create a new instance that should
     * be setup with the IoC Container etc
     * 
     * @return QueryConditionInterface
     */
    public function newInstance();


    /**
     * Function to set the property which the results 
     * should be ordered by
     * 
     * @param string $propertyName
     */
    public function setOrderByField($propertyName);


    /**
     * Function to set the direction which the results
     * should be sorted by, ascending, descending.
     * 
     * @param string $direction
     */
    public function setOrderDirection($direction);


    /**
     * Getter function to return the string that represents
     * the ascending sort direction
     * 
     * @return string
     */
    public function getOrderDirectionAscending();


    /**
     * Getter function to return the string that represents
     * the descending sort direction
     * 
     * @return string
     */
    public function getOrderDirectionDescending();


    /**
     * Function to add all the result ordering directives that have been
     * given to the class to a given request object
     * 
     * @param Guzzle\Http\Message\Request $request Request passed by reference
     * @return  void
     */
    public function addToRequest(&$request);
}
