<?php return array(

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

);
