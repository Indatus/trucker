<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Finders;

use Illuminate\Container\Container;
use Trucker\Facades\AuthFactory;
use Trucker\Facades\Config;
use Trucker\Facades\RequestFactory;
use Trucker\Facades\UrlGenerator;
use Trucker\Finders\Conditions\QueryConditionInterface;
use Trucker\Finders\Conditions\QueryResultOrderInterface;
use Trucker\Resource\Model;
use Trucker\Responses\Collection;

/**
 * Class for finding collections of models over the remote API
 *
 * @author Brian Webb <bwebb@indatus.com>
 */
class CollectionFinder
{

    /**
     * The IoC Container
     *
     * @var Illuminate\Container\Container
     */
    protected $app;

    /**
     * Build a new CollectionFinder
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Function to fetch a collection of Trucker\Resource\Model object
     * from the remote API.
     *
     * @param  Model                      $model       Instance of entity type being fetched
     * @param  QueryConditionInterface    $condition   Query conditions for the request
     * @param  QueryResultOrderInterface  $resultOrder Result ordering requirements for the request
     * @param  array                      $getParams   Additional GET parameters to send w/ request
     * @return Trucker\Responses\Collection
     */
    public function fetch(
        Model $model,
        QueryConditionInterface $condition = null,
        QueryResultOrderInterface $resultOrder = null,
        array $getParams = []
    ) {

        //get a request object
        $request = RequestFactory::build();

        //init the request
        $request->createRequest(
            Config::get('request.base_uri'),
            UrlGenerator::getCollectionUri($model),
            'GET'
        );

        //add auth if it is needed
        if ($auth = AuthFactory::build()) {
            $request->authenticate($auth);
        }

        //add query conditions if needed
        if ($condition) {
            $request->addQueryCondition($condition);
        }

        //add result ordering if needed
        if ($resultOrder) {
            $request->addQueryResultOrder($resultOrder);
        }

        //set any get parameters on the request
        $request->setGetParameters($getParams);

        //actually send the request
        $response = $request->sendRequest();

        //get api response
        $data = $response->parseResponseToData();

        //make an array to hold results
        $records = array();

        //figure out wether a collection key is used
        $collection_key = Config::get('resource.collection_key');

        //set records array appropriatley
        if (isset($collection_key)) {
            $recordCollection = $data[$collection_key];
        } else {
            $recordCollection = $data;
        }

        //create an array of popuplated results
        foreach ($recordCollection as $values) {
            $instance = new $model($values);

            //inflate the ID property that should be guarded
            $id = $instance->getIdentityProperty();
            if (array_key_exists($id, $values)) {
                $instance->{$id} = $values[$id];
            }

            //add the instance to the records array
            $records[] = $instance;

        } //end foreach

        //create a collection object to return
        $collection = new Collection($records);

        // if there was a collection_key, put any extra data that was returned
        // outside the collection key in the metaData attribute
        if (isset($collection_key)) {
            $collection->metaData = array_diff_key($data, array_flip((array) array($collection_key)));
        }

        return $collection;
    }
}
