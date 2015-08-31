<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Url;

use Illuminate\Container\Container;
use Doctrine\Common\Inflector\Inflector;

class UrlGenerator
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;


    /**
     * Build a new UrlGenerator
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }


    /**
     * Getter to access the IoC Container
     * 
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }


    /**
     * Function to get the URI with placeholders for data
     * that a POST request should be made to in order to create
     * a new entity.
     *
     * @param  Trucker\Resource\Model
     * @param  $options Array of options to replace placeholders with
     * @return string
     */
    public function getCreateUri($model, $options = array())
    {
        return $this->getCollectionUri($model, $options);
    }


    /**
     * Function to get the URI with placeholders for data
     * that a PUT / PATCH request should be made to in order to
     * update an existing entity.
     *
     * @param  Trucker\Resource\Model
     * @param  $options Array of options to replace placeholders with
     * @return string
     */
    public function getUpdateUri($model, $options = array())
    {
        return $this->getInstanceUri($model, $options);
    }


    /**
     * Function to get the URI with placeholders for data
     * that a DELETE request should be made to in order to delete
     * an existing entity.
     *
     * @param  Trucker\Resource\Model
     * @param  $options Array of options to replace placeholders with
     * @return string
     */
    public function getDeleteUri($model, $options = array())
    {
        return $this->getInstanceUri($model, $options);
    }


    /**
     * Function to get the URI with placeholders for data
     * that a GET request should be made to in order to retreive
     * a collection of Entities
     *
     * @param  Trucker\Resource\Model
     * @param  $options Array of options to replace placeholders with
     * @return string
     */
    public function getCollectionUri($model, $options = array())
    {
        $uri = $this->getURI($model);
        foreach ($options as $key => $value) {
            $uri = str_replace($key, $value, $uri);
        }

        return $uri;
    }


    /**
     * Function to get the URI with placeholders for data
     * that a GET request should be made to in order to retreive
     * an instance of an Entity
     *
     * @param  Trucker\Resource\Model
     * @param  $options Array of options to replace placeholders with
     * @return string
     */
    public function getInstanceUri($model, $options = array())
    {
        $uri = implode("/", array($this->getURI($model), ':'.$model->getIdentityProperty()));
        foreach ($options as $key => $value) {
            $uri = str_replace($key, $value, $uri);
        }

        return $uri;
    }


    /**
     * Function to return the name of the URI to hit based on
     * the interpreted name of the class in question.  For example
     * a Person class would resolve to /people
     *
     * @param  Trucker\Resource\Model
     * @return string   The URI to hit
     */
    public function getURI($model)
    {
        if ($uri = $model->getURI()) {
            return $uri;
        }

        $uri = Inflector::pluralize(
            Inflector::tableize(
                $model->getResourceName()
            )
        );

        $uriResult = [];
        if (!empty($model->nestedUnder)) {
            $nesting = array_map(
                function ($item) {
                    return explode(':', trim($item));
                },
                explode(',', $model->nestedUnder)
            );
            foreach ($nesting as $nest) {
                list($klass, $entityIdSegment) = $nest;
                if (!is_numeric($entityIdSegment)) {
                    $entityIdSegment = ":$entityIdSegment";
                }

                $entityTypeSegment = Inflector::pluralize(Inflector::tableize($klass));
                $uriResult[] = $entityTypeSegment;
                $uriResult[] = $entityIdSegment;
            }
            $uri = implode("/", $uriResult) . "/$uri";
        }

        $prefix = Config::get('request.path_prefix', '/');
        return "{$prefix}{$uri}";
    }
}
