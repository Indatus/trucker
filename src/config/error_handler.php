<?php return array(

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

);
