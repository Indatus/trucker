<?php

return [
    'auth' => array(

        /*
        |--------------------------------------------------------------------------
        | Authentication Driver
        |--------------------------------------------------------------------------
        |
        | This parameter specifies the driver to use for authenticating requests
        | with the remote API.
        |
        | Supported Options: basic
        |
        | basic - This driver will use HTTP Basic Authentication, and set the
        |         `auth.basic.username` and `auth.basic.password` config values
        |         on the request.
        |
        |
         */

        'driver' => null,

        //credentials for HTTP Basic Authentication
        'basic' => [
            'username' => '{basic_username}',
            'password' => '{basic_password}',
        ],

    ),

    'error_handler' => array(

        /*
        |--------------------------------------------------------------------------
        | Error Handler Driver
        |--------------------------------------------------------------------------
        |
        | This parameter specifies the driver to use for interpreting error messages
        | provided by the remote API.
        |
        | Supported Options: array_response, parameter_key
        |
        |
        | array_response - This driver assumes that when there is an error any error
        |                  messages will be given as the full response body as an array.
        |
        | parameter_key - This driver uses finds the response parameter with a key
        |                 that matches what is defined in `errors_key` and parses the
        |                 error messages contained therein.
        |
         */

        'driver' => 'array_response',

        /*
        |--------------------------------------------------------------------------
        | API Errors Key
        |--------------------------------------------------------------------------
        |
        | When returning a response for an API action this element may contain a
        | string or an array of errors that prevented the success of the request.
        |
         */

        'errors_key' => null,

    ),

    'query_condition' => array(

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

    ),

    'request' => array(

        /*
        |--------------------------------------------------------------------------
        | API endpoint URI
        |--------------------------------------------------------------------------
        |
        | This is the base URI that your API requests will be made to.
        | It should be in a format such as http://my-endpoint.com
        |
         */

        'base_uri' => 'http://example.com',

        /*
        |--------------------------------------------------------------------------
        | HTTP Request Driver
        |--------------------------------------------------------------------------
        |
        | This parameter specifies the driver to use for making HTTP requests to
        | the remote API. The driver handles how the requests are made, formatted etc.
        |
        | Supported Options: rest
        |
        | rest - The rest driver makes HTTP request using GET, PUT, POST and DELETE
        |        HTTP methods to indicate what type of CRUD operation should be completed.
        |        Optional usage of the http_method_param config option can also be used.
        |
         */

        'driver' => 'rest',

        /*
        |--------------------------------------------------------------------------
        | HTTP method request parameter
        |--------------------------------------------------------------------------
        |
        | This is a parameter to send with the request that will contain
        | a string disclosing the desired HTTP method ('put', 'post', 'patch',
        | 'delete').  If specified PUT, POST, PATCH and DELETE requests will
        | all be made as a POST and the given parameter will be added
        | with the http method as it's value. An example might be "_method".
        |
        | Otherwise a true PUT, POST, PATCH or DELETE request will be made
        |
         */

        'http_method_param' => null,

    ),

    'response' => array(

        /*
        |--------------------------------------------------------------------------
        | Response Interpreter Driver
        |--------------------------------------------------------------------------
        |
        | This parameter specifies the driver to use for interpreting API responses
        | as successful, invalid, error etc.
        |
        | Supported Options: http_status_code
        |
        | http_status_code - This driver uses status codes set in the `http_status`
        |                    config section.
        |
         */

        'driver' => 'http_status_code',

        /*
        |--------------------------------------------------------------------------
        | HTTP Status Codes
        |--------------------------------------------------------------------------
        |
        | When making API requests the HTTP Status code returned with the data will
        | be used to determine the success or error of the request.
        |
         */

        'http_status' => array(

            //successful request
            'success' => [200, 201],

            //not found
            'not_found' => 404,

            //invalid request. i.e. an entity couldn't be saved
            'invalid' => 422,

            //an error was encountered when processing the request
            'error' => 500,

        ),

    ),

    'resource' => array(

        /*
        |--------------------------------------------------------------------------
        | Resource Identity Property
        |--------------------------------------------------------------------------
        |
        | This setting defines the response property that contains a remote resource's
        | unique identity property.
        |
         */

        'identity_property' => 'id',

        /*
        |--------------------------------------------------------------------------
        | API Collection Key
        |--------------------------------------------------------------------------
        |
        | When returning a collection of items ( /products for example ) if your API
        | provides the collection within a sub element of the response it can be defined
        | here.
        |
         */

        'collection_key' => null,

        /*
        |--------------------------------------------------------------------------
        | Base64 Property Indication
        |--------------------------------------------------------------------------
        |
        | When working with an entity that will have properties which contain a
        | file the property's value may be set as a Base64 encoded string that
        | contains the file contents.  Before sending to the API endpoint the
        | Base64 string will be written to a file at the scratch_disk_location,
        | then added to the HTTP Request using HTTP-Chunk-Encoding.
        |
        | This config setting provides a way to indicate that the property value
        | to be set contains Base64 encoded content.  The indication can be anywhere
        | in the property name.
        |
        | Example: ( 'base_64_property_indication' => '_base64' )
        |
        | $instance->avatar_base64 = $base64EncodedAvatarImageString;
        | echo $instance->avatar; // => /tmp/tmp_avatar_52dad37453c67.jpg
        |
         */

        'base_64_property_indication' => '_base64',

        /*
        |--------------------------------------------------------------------------
        | Scratch Disk location
        |--------------------------------------------------------------------------
        |
        | This is a filesystem path where temporary files could be written if needed.
        |
        | An example would be an Entity attribute that is a file (via base64 encoded
        | string).  The file would be written to the scratch disk before sending to
        | the endpoint, then sent with the request via HTTP chunked transfer encoding.
        |
         */

        'scratch_disk_location' => '/tmp',

    ),

    'result_order' => array(

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

    ),

    'transporter' => array(

        /*
        |--------------------------------------------------------------------------
        | API Transport Method
        |--------------------------------------------------------------------------
        |
        | This setting defines the transport method for data to and from the remote
        | API endpoint.
        |
        | Supported methods are: json
        |
         */

        'driver' => 'json',

    ),
];
