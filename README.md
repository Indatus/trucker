# Trucker

Trucker is a PHP package for mapping remote API resources (usually RESTful) as models in an ActiveResource style. The benefit is easier use of remote APIs in a fast and clean programming interface.

```php
<?php

//create a class to use
class Product extends Trucker {}

//create a new entity
$p = new Product(['name' => 'My Test Product']);
$success = $p->save();
echo $p->getId();

//find an existing entity
$found = Product::find(1);

//update an entity
$found->name = 'New Product Name';
$success = $found->save();

//destroy an entity
$success = $found->destroy();

//find a collection
$results = Product::all();
```

## README Contents

* [Installation](#install)
    * [Requirements](#requirements)
    * [Install With Composer](#install-composer)
* [Configuration Options](#config)
  * [Main config settings](#config-main)
  * [Search config settings](#config-search)
  * [HTTP status config settings](#config-http-status)
* [Creating an entity](#entity-create)
* [Working with your entity](#entity-work)
  * [Fetching Records](#fetch)
    * [Fetch an Instance](#fetch-instance)
    * [Fetch a Collection](#fetch-collection)
      * [Fetch a collection using query conditions and result ordering](#fetch-collection-complex)
  * [Create, Update & Destroy Operations](#crud)
    * [Create](#crud-create)
    * [Update](#crud-update)
    * [Destroy](#crud-destroy)
* [Advanced Features](#advanced) 


<a name="install"/>
## Installation

<a name="requirements"/>
### Requirements

- Any flavour of PHP 5.4+ should do
- [optional] PHPUnit to execute the test suite

<a name="install-composer"/>
### Install With Composer

You can install the library via [Composer](http://getcomposer.org) by adding the following line to the **require** block of your *composer.json* file:

````
"indatus/trucker": "dev-master"
````

Next run `composer install`

<a name="config"/>
## Configuration Options

Trucker comes with its own configuration file where you can specify options that are constant to your configuration.

<a name="config-main"/>
### Main config settings

* `base_uri` - default: `null`
  * This is the base URI that your API requests will be made to.  It should be in a format such as http://my-endpoint.com
  
* `request_driver` - default: `rest`
  * This parameter specifies the driver to use for making HTTP requests to the remote API. The driver handles how the requests are made, formatted etc.
  * Supported Options: 
    * `rest` - The rest driver makes HTTP request using GET, PUT, POST and DELETE HTTP methods to indicate what type of CRUD operation should be completed. Optional usage of the `http_method_param` config option can also be used.

* `http_method_param` - default: `null`
  * This is a parameter to send with the request that will contain a string disclosing the desired HTTP method ('put', 'post', 'patch', 'delete' etc.).  If specified PUT, POST, PATCH and DELETE requests will all be made as a POST and the given parameter will be added with the http method as it's value. An example might be "_method". 
  * Otherwise a true PUT, POST, PATCH or DELETE request will be made 

* `scratch_disk_location` - default: `/tmp`
  * This is a filesystem path where temporary files could be written if needed.
  * An example would be an Entity attribute that is a file (via base64 encoded string).  The file would be written to the scratch disk before sending to the endpoint, then sent with the request via HTTP chunked transfer encoding.

* `transporter` - default: `json`
  * This setting defines the transport method for API endpoint.  
  * Supported Options
    * `json`

* `identity_property` - default: `id`
  * This setting defines the response property that contains a remote resource's unique identity property.

* `collection_key` - default: `null`
  * When returning a collection of items ( /products for example ) if your API provides the collection within a sub element of the response it can be defined here.

* `error_response_driver` - default: `response_param_array`
  * This setting details which driver to use for handling any error messages provided by the remote API when a request encounters an error
  * Supported Options: 
    * `response_param_array` - Errors are returned in the response as an array contained in the key defined in the `errors_key` config parameter

* `errors_key` - default: `null`
  * When returning a response for an API action this element may contain a string or an array of errors that prevented the success of the request.

* `base_64_property_indication` - default: `_base64`
  * When working with an entity that will have properties which contain a file the property's value may be set as a Base64 encoded string that  contains the file contents.  Before sending to the API endpoint the Base64 string will be written to a file at the `scratch_disk_location`, then added to the HTTP Request using HTTP-Chunk-Encoding.  
  * This config setting provides a way to indicate that the property value to be set contains Base64 encoded content.  The indication can be anywhere in the property name.
  * Example config setting: `'base_64_property_indication' => '_base64'`
  * Example usage: 
  ````
  $instance->avatar_base64 = $base64EncodedAvatarImageString;
  echo $instance->avatar; // => /tmp/tmp_avatar_52dad37453c67.jpg
  ````

<a name="config-search"/>
### Search config settings

When making a request for a collection you may specify conditions similar to a SQL WHERE clause.  These will be sent along with your request as an array parameter which contains a grouping of key / values that define the set of conditions.

* `collection_query_condition_driver` - default: `get_array`
  * This setting tells Trucker how to give directives to the remote API which govern how conditions on a collection fetch shoud be conveyed.
  * Supported Options:
    * `get_array` - This option will send the collection query conditions as an array of GET parameters nested under the `search.container_parameter` parameter defined in the config.  The resulting GET parameters may look something like:
    ````    
    search[0][property]=someProperty
    search[0][operator]=<
    search[0][value]=1234
    search[1][property]=anotherProperty
    search[1][operator]=LIKE
    search[1][value]=someString
    logical_operator=AND
    ````

* `collection_result_order_driver` - default: `get_param`
  * This setting tells Trucker how give directives to the remote API which govern how collection results should be ordered.
  * Supported Options:
  * `get_param` - This option will send the property to order results by, and the sort direction as GET parameters on the request. The parameters are specified in `search.order_by` and `search.order_dir`.  The resulting GET parameters may look something like:
  ````
  order_by=someProperty
  order_dir=ASC
  ````

* `search.container_parameter` - default: `search`
  * The request parameter which will contain the array of search conditions

* `search.property` - default: `property`
  * The name of the parameter key used to identify an attribute of a remote entity

* `search.operator` - default: `operator`
  * Name of the parameter key used to specify a search rule operator
  * i.e.: `=`, `>=`, `<=`, `!=`, `LIKE`

* `search.value` - default: value
  * Name of the parameter key used to identify an entity value when providing search conditions

* `search.logical_operator` - default: `logical_operator`
  * Name of the parameter key used to identify how search criteria should be combined when multiples are present

* `search.order_by` - default: `order_by`
  * Name of the parameter key used to identify the property to order search results by

* `search.order_dir` - default: `order_dir`
  * Name of the parameter key used to identify the order direction of search results when providing the `order_by` parameter

* `search.and_operator` - default: `AND`
  * Name of the parameter value for specifying "AND" search rule combination behavior

* `search.or_operator` - default: `OR`
  * Name of the parameter value for specifying "OR" search rule combination behavior

* `search.order_dir_ascending` - default: `ASC`
  * Name of the parameter value for specifying ascending result ordering

* `search.order_dir_descending` - default: `DESC`
  * Name of the parameter value for specifying descending result ordering

<a name="config-http-status"/>
### HTTP status config settings

When making API requests the HTTP Status code returned with the data will be used to determine the success or error of the request.

* `http_status.success` - default: `200`
  * successful request

* `http_status.not_found` - default: `401`
  * not found

* `http_status.invalid` - default: `422`
  * invalid request. i.e. an entity couldn't be saved

* `http_status.error` - default: `500`
  * an error was encountered when processing the request

<a name="entity-create"/>
## Creating an entity

Now you can create an entity object for a noun in your API (this is the minimum code you'll need to get started):

```php
<?php
class Product extends Trucker
{

}
```
    
Trucker uses convention over configuration, so it will infer what the URI should be based on your class name.  In the example of 'Product' the URI will be assumed to be */products*.

<a name="entity-work"/>
## Working with your entity

Now that you have Trucker object you can use it with CRUD operations as you may expect you would with an ORM.

<a name="fetch"/>
### Fetching Records

Trucker splits fetching records over your API into 2 categories.  Getting an instance and getting a collection.

<a name="fetch-instance"/>
#### Fetch an Instance

If you have an entity where you know the value of it's `identity_property` you can fetch it with the `find()` method.  

`find()` takes a second parameter as well that allows you to pass in an arbitrary associative array that you want to be converted into query string arguments that get sent with the request.

```php
$p = Product::find(1);
```

<a name="fetch-collection"/>
#### Fetch a Collection

When you want to fetch a collection of records you can use the `all()` function.

```php
$results = Product::all();
```

The `all()` function takes arguments that allow you to specify conditions on the results that you'll get back.  How the request will be made to the API depends on `collection_query_condition_driver` and `collection_result_order_driver` you are using.

<a name="fetch-collection-complex"/>
##### Fetch a collection using query conditions and result ordering

```php
$conditions = ConditionFactory::build();
$conditions->addCondition('name', '=', 'Roomba 650');
$conditions->addCondition('vendor', '=', 'Irobot');
$conditions->addCondition('price', '>=', 10000);
$conditions->setLogicalOperator($conditions->getLogicalOperatorAnd());

$order = ResultOrderFactory::build();
$order->setOrderByField('name');
$order->setOrderDirection($order->getOrderDirectionDescending());

$results = Product::all($conditions, $order);
```

You may also provide a third array parameter to the `all()` function containing an associative array of values to include in the request as querystring parameters.

<a name="crud"/>
### Create, Update & Destroy Operations

<a name="crud-create"/>
#### Create

```php
$attributes = ['name' => 'XYZ Headphones', 'vendor' => 'Acme', 'price' => '10000'];

//pass attributes to the constructor
$p = new Product($attributes);

//or use the fill() method
$p = new Product;
$p->fill($attributes);

//get the attributes back if you want to see them
print_r($p->attributes());
// => ['name' => 'XYZ Headphones', 'vendor' => 'Acme', 'price' => '10000']

//maybe you want to see a particular property
echo $p->name; // => XYZ Headphones

//or modify a property
$p->name = "ABC Headphones";

//save the object over the API.  The save() method will create or update 
//the object as necessary.  It returns true or false based on success.
$success = $p->save(); 

if ($success) {

  //the identity property is set back on the object after it is created
  echo "Saved product '{$p->name}' with ID: ". $p->getId();

} else {

  //maybe you want to print out the errors if there were some
  echo "Errors: ". implode(", ", $p->errors());
  // => ['Category is required', 'Cost must be greater than 0']
}
```

<a name="crud-update"/>
#### Update

Update works quite similar to the create functionality, from the code perspective it is nearly identicial.

```php
$p = Product::find(1);
$p->name = "My Product";

if ($p->save()) {
   echo "Updated!";
} else {
   echo "Error: ". implode(", ", $p->errors());
}
```

<a name="crud-destroy"/>
#### Destroy

The destroy function requires an existing instance, and returns a boolen based on the success of the request.

```php
$p = Product::find(1);

if ($p->destroy()){
  echo "Deleted product: {$p->name}";
} else {
  echo "Error deleting product: {$p->name}";
  echo "Errors: ". implode(", ", $p->errors());
}
```

<a name="advanced"/>
## Advanced Features

Trucker uses sensible defaults for its default configuration, but allows you to customize it via the config settings.  Additionally you can override the config settings for an individual class by overriding protected properties on the class by setting concrete values or overriding at runtime.
