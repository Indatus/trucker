<?php return array(

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

);
