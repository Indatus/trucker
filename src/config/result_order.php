<?php return array(

    /*
    |--------------------------------------------------------------------------
    | Collection Result Order Driver
    |--------------------------------------------------------------------------
    |
    | This setting tells Trucker how give directives to the remote API 
    | which govern how collection results should be ordered.
    |
    | Supported Options:
    |
    | get_param - This option will send the property to order results by, 
    |             and the sort direction as GET parameters on the request.
    |             The parameters are specified in search.order_by and 
    |             search.order_dir
    |
    */
   
    'driver' => 'get_params',

    /*
    |--------------------------------------------------------------------------
    | Get Params Driver Config
    |--------------------------------------------------------------------------
    |
    | Config values that are specific to the 'get_params' driver
    |
    */

    'get_params' => [

        // Name of the parameter key used to identify the property to order
        // search results by
        'order_by' => 'order_by',

        // Name of the parameter key used to identify the order direction
        // of search results when providing the 'order_by' parameter
        'order_dir' => 'order_dir',

        // Name of the parameter value for specifying ascending result ordering
        'order_dir_ascending' => 'ASC',

        // Name of the parameter value for specifying descending result ordering
        'order_dir_descending' => 'DESC',

    ],

);
