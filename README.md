# Trucker

[<img src="https://s3-us-west-2.amazonaws.com/oss-avatars/trucker.png"/>](http://indatus.com/company/careers)

[![Latest Stable Version](https://poser.pugx.org/indatus/trucker/v/stable.png)](https://packagist.org/packages/indatus/trucker) [![Total Downloads](https://poser.pugx.org/indatus/trucker/downloads.png)](https://packagist.org/packages/indatus/trucker) [![Build Status](https://travis-ci.org/Indatus/trucker.png?branch=master)](https://travis-ci.org/Indatus/trucker) [![Coverage Status](https://coveralls.io/repos/Indatus/trucker/badge.png?branch=master)](https://coveralls.io/r/Indatus/trucker?branch=master) [![Dependency Status](https://www.versioneye.com/user/projects/530271f4ec1375bab10004bb/badge.png)](https://www.versioneye.com/user/projects/530271f4ec1375bab10004bb)

Trucker is a PHP package for mapping remote API resources (usually RESTful) as models in an ActiveResource style. The benefit is easier use of remote APIs in a fast and clean programming interface.

<!--
<img align="left" height="300" src="https://s3-us-west-2.amazonaws.com/oss-avatars/trucker_round_readme.png">
-->


```php
<?php

class Product extends Trucker\Resource\Model {} //create a class to use

$p = new Product(['name' => 'My Test Product']);
$success = $p->save(); //create a new entity

$found = Product::find(1); //find an existing entity

$found->name = 'New Product Name';
$success = $found->save(); //update an entity

$success = $found->destroy(); //destroy an entity

$results = Product::all(); //find a collection
```

## README Contents

* [Installation](#install)
    * [Requirements](#requirements)
    * [Install With Composer](#install-composer)
      * [Configure in Laravel](#config-laravel)
      * [Configure outside Laravel](#config-non-laravel)
* [Configuration Options](#config)
  * [Auth](#config-auth)
  * [Error Handler](#config-error-handler)
  * [Query Condition](#config-query-condition)
  * [Request](#config-request)
  * [Resource](#config-resource)
  * [Response](#config-response)
  * [Result Order](#config-result-order)
  * [Transporter](#config-transporter)
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
* [Customizing Entities](#customizing-entities) 
  * [Setting concrete class properties](#custom-concrete)
  * [Setting runtime properties](#custom-runtime)
  * [Setting config values at runtime](#custom-config-runtime)


<a name="install"/>
## Installation

<a name="requirements"/>
### Requirements

- Any flavour of PHP 5.4+ should do

<a name="install-composer"/>
### Install With Composer

You can install the library via [Composer](http://getcomposer.org) by adding the following line to the **require** block of your *composer.json* file:

````
"indatus/trucker": "dev-master"
````

Next run `composer install`, now you need to publish the config files.

Trucker's config files are where you'd define constant things about the API your interacting with, like the end-point, what drivers you want to use etc.

<a name="config-laravel"/>
### Configure in Laravel

Trucker works well with the [Laravel](http://laravel.com) framework.  If your using Trucker within Laravel, you just need to run the following command to publish the Trucker config files to the **app/config/packages/indatus/trucker** folder.

````
php artisan config:publish indatus/trucker
````

The final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'Trucker\TruckerServiceProvider'

Now you should be ready to go.    

<a name="config-non-laravel"/>
### Configure outside Laravel

If your using Trucker outside Laravel you just need to create the `.trucker` folder in your project root and copy the package's config files there.  Here's the *nix command for that.

````
mkdir .trucker && cp vendor/indatus/trucker/src/config/* .trucker/
````

<a name="config"/>
## Configuration Options

Trucker comes with its own configuration file where you can specify options that are constant to your configuration.

<a name="config-auth" />
### Auth (auth.php)

Setting | Default | Description
--- | --- | ---
`driver` | `null` | This parameter specifies the driver to use for authenticating requests with the remote API.<br /><br />_Supported Options:_ `basic`
`basic.username` | `null` | `basic` driver option: HTTP Basic Authentication username
`basic.password` | `null` | `basic` driver option: HTTP Basic Authentication password

> **Supported Option Details:**

Option | Details
--- | ---
`basic` | This driver will use HTTP Basic Authentication, and set the `auth.basic.username` and `auth.basic.password` config values on the request.<br /><br />_Supported Options:_ `array_response`, `parameter_key`


<a name="config-error-handler" />
### Error Handler (error_handler.php)

Setting | Default | Description
--- | --- | ---
`driver` | `array_response` | This parameter specifies the driver to use for interpreting error messages provided by the remote API.
`errors_key` | `null` | When returning a response for an API action this element may contain a string or an array of errors that prevented the success of the request.

> **Supported Option Details:**

Option | Details
--- | ---
`array_response` | This driver assumes that when there is an error any error messages will be given as the full response body as an array.
`parameter_key` | This driver uses finds the response parameter with a key that matches what is defined in `errors_key` and parses the error messages contained therein.


<a name="config-query-condition" />
### Query Condition (query_condition.php)

When making a request for a collection you may specify conditions similar to a SQL WHERE clause.  These will be sent along with your request as an array parameter which contains a grouping of key / values that define the set of conditions.

Setting | Default | Description
--- | --- | ---
`driver` | `get_array_params` | This setting tells Trucker how to give directives to the remote API which govern how conditions on a collection fetch shoud be conveyed.<br /><br />_Supported Options:_ `get_array_params`
`get_array_params.container_parameter` | `search` | `get_array_params` driver option: The request parameter which will contain the array of search conditions
`get_array_params.property` | `property` | `get_array_params` driver option: The name of the parameter key used to identify an attribute of a remote entity
`get_array_params.operator` | `operator` | `get_array_params` driver option: Name of the parameter key used to specify a search rule operator i.e.: `=`, `>=`, `<=`, `!=`, `LIKE`
`get_array_params.value` | value | `get_array_params` driver option: Name of the parameter key used to identify an entity value when providing search conditions
`get_array_params.logical_operator` | `logical_operator` | `get_array_params` driver option: Name of the parameter key used to identify how search criteria should be combined when multiples are present
`get_array_params.and_operator` | `AND` | `get_array_params` driver option: Name of the parameter value for specifying "AND" search rule combination behavior
`get_array_params.or_operator` | `OR` | `get_array_params` driver option: Name of the parameter value for specifying "OR" search rule combination behavior


> **Supported Option Details:**

Option | Details
--- | ---
`get_array_params` | This option will send the collection query conditions as an array of GET parameters nested under the `get_array_params` key defined in the config.

> The resulting GET parameters may look something like:

````    
search[0][property]=someProperty
search[0][operator]=<
search[0][value]=1234
search[1][property]=anotherProperty
search[1][operator]=LIKE
search[1][value]=someString
logical_operator=AND
````


<a name="config-request" />
### Request (request.php)

Setting | Default | Description
--- | --- | ---
`base_uri` | `null` | This is the base URI that your API requests will be made to.  It should be in a format such as http://my-endpoint.com
`path_prefix` | `null` | This lets you set a prefix for all API requests - eg. /api/
  defaults to '/' if nothing is set
`driver` | `rest` | This parameter specifies the driver to use for making HTTP requests to the remote API. The driver handles how the requests are made, formatted etc.<br /><br />_Supported Options:_ `rest`
`http_method_param` | `null` | This is a parameter to send with the request that will contain a string disclosing the desired HTTP method ('put', 'post', 'patch', 'delete' etc.).  If specified PUT, POST, PATCH and DELETE requests will all be made as a POST and the given parameter will be added with the http method as it's value. An example might be "_method". <br /><br />Otherwise a true PUT, POST, PATCH or DELETE request will be made 


> **Supported Option Details:**

Option | Details
--- | ---
`rest` | The rest driver makes HTTP request using GET, PUT, POST and DELETE HTTP methods to indicate what type of CRUD operation should be completed. Optional usage of the `http_method_param` config option can also be used.


<a name="config-resource" />
### Resource (resource.php)

Setting | Default | Description
--- | --- | ---
`identity_property` | `id` | This setting defines the response property that contains a remote resource's unique identity property.
`collection_key` | `null` | When returning a collection of items ( /products for example ) if your API provides the collection within a sub element of the response it can be defined here.
`base_64_property_indication` | `_base64` | When working with an entity that will have properties which contain a file the property's value may be set as a Base64 encoded string that  contains the file contents.  Before sending to the API endpoint the Base64 string will be written to a file at the `scratch_disk_location`, then added to the HTTP Request using HTTP-Chunk-Encoding. <br /><br />This config setting provides a way to indicate that the property value to be set contains Base64 encoded content.  The indication can be anywhere in the property name. <br /><br />Example config setting: `'base_64_property_indication' => '_base64'`<br /><br />Example usage:<br />`$instance->avatar_base64 = $base64EncodedAvatarImageString;`<br />`echo $instance->avatar; // => /tmp/tmp_avatar_52dad37453c67.jpg`
`scratch_disk_location` | `/tmp` | This is a filesystem path where temporary files could be written if needed.<br /><br />An example would be an Entity attribute that is a file (via base64 encoded string).  The file would be written to the scratch disk before sending to the endpoint, then sent with the request via HTTP chunked transfer encoding.


<a name="config-response" />
### Response (response.php)

Setting | Default | Description
--- | --- | ---
`driver` | `http_status_code` | This parameter specifies the driver to use for interpreting API responses as successful, invalid, error etc.<br /><br />_Supported Options:_ `http_status_code`
`http_status.success` | `200`, `201` | `http_status_code` driver option: successful request
`http_status.not_found` | `404` | `http_status_code` driver option: not found
`http_status.invalid` | `422` | `http_status_code` driver option: invalid request. i.e. an entity couldn't be saved
`http_status.error` | `500` | `http_status_code` driver option: an error was encountered when processing the request

_Wildcards may be used to match what an http code **starts with** (e.g - `20*`)._

> **Supported Option Details:**

Option | Details
--- | ---
`http_status_code` | This driver uses status codes set in the `http_status` config section to determine the success or error of the request.


<a name="config-result-order" />
### Result Order (result_order.php)

Setting | Default | Description
--- | --- | ---
`driver` | `get_params` | This setting tells Trucker how give directives to the remote API which govern how collection results should be ordered.<br /><br />_Supported Options:_ `get_params`
`get_params.order_by` | `order_by` | `get_params` driver option: Name of the parameter key used to identify the property to order search results by
`get_params.order_dir` | `order_dir` | `get_params` driver option: Name of the parameter key used to identify the order direction of search results when providing the `order_by` parameter
`get_params.order_dir_ascending` | `ASC` | `get_params` driver option: Name of the parameter value for specifying ascending result ordering
`get_params.order_dir_descending` | `DESC` | `get_params` driver option: Name of the parameter value for specifying descending result ordering

> **Supported Option Details:**

Option | Details
--- | ---
`get_params` | This option will send the property to order results by, and the sort direction as GET parameters on the request. The parameters are specified in `get_params.order_by` and `get_params.order_dir`.  The resulting GET parameters may look something like: `order_by=someProperty,order_dir=ASC`


<a name="config-transporter" />
### Transporter (transporter.php)

Setting | Default | Description
--- | --- | ---
`driver` | `json` | This setting defines the transport method for API endpoint.<br /><br />_Supported Options:_ `json`  


<a name="entity-create"/>
## Creating an entity

Now you can create an entity object for a noun in your API (this is the minimum code you'll need to get started):

```php
<?php
class Product extends Trucker\Resource\Model
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

```php
$p = Product::find(1);
```
> **Optional 2nd arg:** `find()` takes a second parameter as well that allows you to pass in an arbitrary associative array that you want to be converted into query string arguments that get sent with the request.

> **Optional 3rd arg:** `find()` takes a third parameter as well that allows you to pass in an object of the class your finding on with properties that have been set at runtime.  The `find()` function will use this object for interperting the URL if given, other wise it will call `new static;` on the class `find()` is called on.


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

> **Note:** You may also provide a third array parameter to the `all()` function containing an associative array of values to include in the request as querystring parameters.

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

<a name="customizing-entities"/>
## Customizing Entities

Trucker uses sensible defaults for its default configuration, but allows you to customize it via the config settings.  Additionally you can override the config settings for an individual class by overriding properties on the concrete implementation; by setting values at runtime on the class or in the config.

<a name="custom-concrete"/>
### Setting concrete class properties.
  
The following fields can be set on a class implementation to override interpreted values, default values or just to define the class functionality.  

Property | Visibility | Type | Description
--- | --- | --- | ---
`resourceName` | protected | string | The name of the resource which is used to determine the resource URI through the use of reflection.  By default if this is not set `getResourceName()` will use the class name.  A `Person` class would  be inflected to _/people_ but you could change that by setting the `resourceName` to something different like "Employee" to get _/employees_.  Alternativley you could set the `uri` property directly. 
`uri` | protected | string | Property to overwrite the `getURI()` function with a static value of what remote API URI path should be used. Should be set with a value with a leading slash if used.
`nestedUnder` | public | string | Property to hold the data about entities for which this resource is nested beneath.  For example if this entity was 'Employee' which was a nested resource under a 'Company' and the instance URI should be /companies/:company_id/employees/:id then you would assign this string with 'Company:company_id'. Doing this will allow you to pass in ':company_id' as an option to the URI creation functions and ':company_id' will be replaced with the value passed.<br /><br />Alternativley you could set the value to something like 'Company:100'.<br /><br />This value can be nested as a comma separated string as well.
`identityProperty` | protected | string | Remote resource's primary key property
`guarded` | protected | string | Comma separated list of properties that can't be set via mass assignment
`fileFields` | protected | string | Comma separated list of properties that will take a file path that should be read in and sent with any API request
`readOnlyFields` | protected | string | Comma separated list of properties that may be in a GET request but should not be added to a create or update request
`scratchDiskLocation` | protected | string | Filesystem location that temporary files could be written to if needed
`base64Indicator` | protected | string | Portion of a property name that would indicate that the value would be Base64 encoded when the property is set.


**Example:**

```php
<?php
class Product extends Trucker\Resource\Model
{
  protected $uri = "/small_electronic_products";
  protected $guarded = "price,sale_price";
  protected $fileFields = "picture";
}
```

<a name="custom-runtime" />
### Setting runtime properties

There may be situations where you can't set a property in the concrete class implementation because it's value is variable and changes at runtime.  For this situation you could set the property before it is used in a request.

**Example:**

```php
<?php
$vendor_id = 9876;
$p = new Product;
$p->nestedUnder = "Vendor:{$vendor_id}";
$found = Product::find(1, [], $p);
//will hit /vendors/9876/products/1
```

<a name="advanced-config-runtime" />
### Setting config values at runtime

There may be times where you need to change values that are set in your Trucker config files at runtime before a request is made that uses those values.  You can use Trucker's config manager for this.

> **Note:** If your using Trucker in [Laravel](http://laravel.com) you'll want to alias the Trucker config manager `Config` to something different like `TruckerConfig` so it doesn't conflict with Laravel's own `Config` class.

**Example:**

```php
<?php
use Trucker\Facades\Config as TruckerConfig;

TruckerConfig::set('auth.basic.username', $someUsername);
TruckerConfig::set('auth.basic.password', $somePassword);
$found = Product::find(1);
```
