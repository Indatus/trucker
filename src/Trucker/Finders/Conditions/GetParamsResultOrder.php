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
use Trucker\Facades\Config;

/**
 * Class to manage how sorting requirements for results returned by
 * a request, where the directives are passed as HTTP GET parameters with
 * particular parameter names (defined in the config).
 * The resulting GET params might be something like:
 *
 * <code>
 * order_by=someProperty
 * order_dir=ASC
 * </code>
 */
class GetParamsResultOrder implements QueryResultOrderInterface
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;

    /**
     * Var to hold the order by field
     * that the results shoudl be sorted on
     *
     * @var string
     */
    protected $orderByField;

    /**
     * Var to hold the order direction
     * for results to be returned
     *
     * @var string
     */
    protected $orderDirection;

    /**
     * Constructor, likely never called in implementation
     * but rather through the service provider
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Function to create a new instance that should
     * be setup with the IoC Container etc
     *
     * @return QueryConditionInterface
     */
    public function newInstance()
    {
        $instance = new static($this->app);
        return $instance;
    }

    /**
     * Function to set the property which the results
     * should be ordered by
     *
     * @param string $propertyName
     */
    public function setOrderByField($propertyName)
    {
        $this->orderByField = $propertyName;
    }

    /**
     * Function to set the direction which the results
     * should be sorted by, ascending, descending.
     *
     * @param string $direction
     */
    public function setOrderDirection($direction)
    {

        if ($direction != $this->getOrderDirectionAscending() &&
            $direction != $this->getOrderDirectionDescending()
        ) {
            throw new Exception("Invalid order direction: {$direction}");
        }

        $this->orderDirection = $direction;
    }

    /**
     * Getter function to return the string that represents
     * the ascending sort direction
     *
     * @return string
     */
    public function getOrderDirectionAscending()
    {
        return Config::get('result_order.get_params.order_dir_ascending');
    }

    /**
     * Getter function to return the string that represents
     * the descending sort direction
     *
     * @return string
     */
    public function getOrderDirectionDescending()
    {
        return Config::get('result_order.get_params.order_dir_descending');
    }

    /**
     * Function to add all the directives that have been
     * given to the class to a given request object
     *
     * @param Guzzle\Http\Message\Request $request Request passed by reference
     * @return  void
     */
    public function addToRequest(&$request)
    {
        $query = $request->getQuery();

        if (isset($this->orderByField)) {
            $query->add(
                Config::get('result_order.get_params.order_by'),
                $this->orderByField
            );
        }

        if (isset($this->orderDirection)) {
            $query->add(
                Config::get('result_order.get_params.order_dir'),
                $this->orderDirection
            );
        }
    }

    /**
     * Function to convert the directives represented in this class
     * to an array, this is useful for testing
     *
     * @return array
     */
    public function toArray()
    {
        $order_by = Config::get('result_order.get_params.order_by');
        $order_dir = Config::get('result_order.get_params.order_dir');

        $params = [];
        $params[$order_by] = $this->orderByField;
        $params[$order_dir] = $this->orderDirection;

        return $params;
    }

    /**
     * Function to convert the directives represented in this class
     * to a querystring, this is useful for testing
     *
     * @return string
     */
    public function toQueryString()
    {
        return http_build_query($this->toArray());
    }
}
