<?php return array(

    /*
    |--------------------------------------------------------------------------
    | Collection Query Condition Driver
    |--------------------------------------------------------------------------
    |
    | This setting tells Trucker how to give directives to the remote API which
    | govern how conditions on a collection fetch shoud be conveyed.
    |
    | Supported Options:
    |
    | get_array - This option will send the collection query conditions as an 
    |             array of GET parameters nested under the search.container_parameter
    |             parameter defined in the config.  The resulting GET parameters
    |             may look something like:
    |
    |             search[0][property]=someProperty
    |             search[0][operator]=<
    |             search[0][value]=1234
    |             search[1][property]=anotherProperty
    |             search[1][operator]=LIKE
    |             search[1][value]=someString
    |             logical_operator=AND
    | 
    |
    |
    */
   
    'driver' => 'get_array_params',


    /*
    |--------------------------------------------------------------------------
    | Get Array Params Driver Config
    |--------------------------------------------------------------------------
    |
    | Config values that are specific to the 'get_array_params' driver
    |
    */
   
    'get_array_params' => [

        // The request parameter which will contain the array of search conditions
        'container_parameter' => 'search',

        // The name of the parameter key used to identify an attribute
        // of a remote entity
        'property' => 'property',

        // Name of the parameter key used to specify a search rule operator
        // i.e.: = >= <= != LIKE
        'operator' => 'operator',

        // Name of the parameter key used to identify an entity value
        // when providing search conditions
        'value' => 'value',

        // Name of the parameter key used to identify how search criteria
        // should be combined when multiples are present
        'logical_operator' => 'logical_operator',

        // Name of the parameter value for specifying "AND" search rule
        // combination behavior
        'and_operator' => 'AND',

        // Name of the parameter value for specifying "OR" search rule
        // combination behavior
        'or_operator' => 'OR',

    ],

);
