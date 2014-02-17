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
 * Interface to dictate management of query conditions for a request
 */
interface QueryConditionInterface
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
     * Function to add a query condition 
     * 
     * @param string $property The field the condition operates on
     * @param string $operator The operator (=, <, >, <= and so on)
     * @param string $value    The value the condition should match
     * @return  void
     */
    public function addCondition($property, $operator, $value);


    /**
     * Function to set the logical operator for the 
     * combination of any conditions that have been passed to the 
     * addCondition() function
     * 
     * @param string $operator
     * @return  void
     */
    public function setLogicalOperator($operator);


    /**
     * Function to get the string representing
     * the AND logical operator
     * 
     * @return string
     */
    public function getLogicalOperatorAnd();


    /**
     * Function to get the string representing
     * the OR logical operator
     * 
     * @return string
     */
    public function getLogicalOperatorOr();


    /**
     * Function to add all the conditions that have been
     * given to the class to a given request object
     * 
     * @param Guzzle\Http\Message\Request $request Request passed by reference
     * @return  void
     */
    public function addToRequest(&$request);
}
