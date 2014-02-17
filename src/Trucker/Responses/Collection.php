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
 * Collection class returned from CollectionFinder when
 * a colleciton of results is requested
 *
 * @author Brian Webb <bwebb@indatus.com>
 */
class Collection implements \Iterator
{
    
    /**
     * Var to hold the actual source array collection
     *
     * @var array
     */
    private $collection;
    

    /**
     * Associative array of metadata related to the
     * collection
     *
     * @var array
     */
    public $metaData = array();

    
    /**
     * Constructor for the collection
     * 
     * @param array $givenArray array of objects
     */
    public function __construct($givenArray)
    {
        $this->collection = $givenArray;
    }
    

    /**
     * Function to conform with Iterator interface.
     *
     * @see  Iterator
     * 
     * @return Trucker\Resource\Model
     */
    public function rewind()
    {
        return reset($this->collection);
    }
    

    /**
     * Function to conform with Iterator interface.
     *
     * @see  Iterator
     * 
     * @return Trucker\Resource\Model
     */
    public function current()
    {
        return current($this->collection);
    }
    

    /**
     * Function to conform with Iterator interface.
     *
     * @see  Iterator
     * 
     * @return Trucker\Resource\Model
     */
    public function key()
    {
        return key($this->collection);
    }
    

    /**
     * Function to conform with Iterator interface.
     *
     * @see  Iterator
     * 
     * @return Trucker\Resource\Model
     */
    public function next()
    {
        return next($this->collection);
    }
    

    /**
     * Function to conform with Iterator interface.
     *
     * @see  Iterator
     * 
     * @return Trucker\Resource\Model
     */
    public function valid()
    {
        return key($this->collection) !== null;
    }
    

    /**
     * Function to return the size of the collection
     * 
     * @return int size of collection
     */
    public function size()
    {
        return count($this->collection);
    }
    

    /**
     * Function to return the first item of the collection
     * 
     * @return Trucker\Resource\Model
     */
    public function first()
    {
        return (empty($this->collection) ? null : $this->collection[0]);
    }
    

    /**
     * Function to return the last item of the collection
     * 
     * @return Trucker\Resource\Model
     */
    public function last()
    {
        return (empty($this->collection) ? null : $this->collection[count($this->collection)-1]);
    }
    

    /**
     * Function to convert the collection to an array using
     * each collection elements attributes
     *
     * @param  string $collectionKey 
     * @param  string $metaKey
     * @return array
     */
    public function toArray($collectionKey = null, $metaKey = 'meta')
    {
        $entities = array();
        foreach ($this->collection as $entity) {
            $entities[] = $entity->attributes();
        }

        if ($collectionKey) {
            $col = [$collectionKey => $entities];
        } else {
            $col = $entities;
        }

        if ($this->metaData) {
            $met = [$metaKey => $this->metaData];
        } else {
            $met = [];
        }
    
        return array_merge($col, $met);
    }
    

    /**
     * Function to convert the collection to json using
     * each collection elements attributes as an array then
     * encoding the array to json
     *
     * @param  string $collectionKey 
     * @param  string $metaKey
     * @return array
     */
    public function toJson($collectionKey = null, $metaKey = 'meta')
    {
        return json_encode($this->toArray($collectionKey, $metaKey));
    }
}//end class
