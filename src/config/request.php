<?php return array(

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
    | API endpoint path
    |--------------------------------------------------------------------------
    |
    | This is the optional path where the API endpoint is located under, 
    | for instance /admin/ would result in http://example.com/admin/
    |
    |
    */
    'path_prefix' => '/',

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

);
