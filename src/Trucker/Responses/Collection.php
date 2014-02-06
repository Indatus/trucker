<?php 

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LICENSE: The BSD 3-Clause
 * 
 * Copyright (c) 2013, Indatus
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list 
 * of conditions and the following disclaimer.
 * 
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other 
 * materials provided with the distribution.
 * 
 * Neither the name of Indatus nor the names of its contributors may be used 
 * to endorse or promote products derived from this software without specific prior 
 * written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES 
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT 
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, 
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT 
 * OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Trucker
 * @author      Brian Webb <bwebb@indatus.com>
 * @copyright   2013 Indatus
 * @license     http://opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause
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
     * @return Trucker\Model
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
     * @return Trucker\Model
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
     * @return Trucker\Model
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
     * @return Trucker\Model
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
     * @return Trucker\Model
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
     * @return Trucker\Model
     */
    public function first()
    {
        return (empty($this->collection) ? null : $this->collection[0]);
    }
    

    /**
     * Function to return the last item of the collection
     * 
     * @return Trucker\Model
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
