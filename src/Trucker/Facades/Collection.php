<?php

/**
 * This file is part of Trucker
 *
 * (c) Brian Webb <bwebb@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Trucker\Facades;

use Illuminate\Support\Facades\Facade;
use Trucker\TruckerServiceProvider;

 /**
  * Facade class for interacting with the Trucker Collection class
  *
  * @author Brian Webb <bwebb@indatus.com>
  */
class Collection extends Facade
{

  /**
   * Get the registered name of the component.
   *
   * @return string
   */
    protected static function getFacadeAccessor()
    {
        if (!static::$app) {
            static::$app = TruckerServiceProvider::make();
        }
        return 'trucker.collection-finder';
    }
}
