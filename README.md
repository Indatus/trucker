## Trucker: A PHP ActiveResource Library

[![build status](http://gitlab-ci.indatus.com/projects/3/status.png?ref=master)](http://gitlab-ci.indatus.com/projects/3?ref=master)

Trucker is a PHP package for accessing REST APIs in an ActiveResource style of coding. The benefit is easier use of REST APIs in a fast and clean programming interface.

The package follows convention over configuration. So you should be able to get up to speed consuming a REST API in a very short time.

#### Installation

You can install the library via [Composer](http://getcomposer.org) by adding the following line to the **require** block of your *composer.json* file:

````
"indatus/trucker": "dev-master"
````

Next run `composer update` or `composer install`

### Examples

#### Setup the config 

... todo: fill this in ...

#### Create a model

Now you can create an model for a noun in your rest API (this is the minimum code you'll need):


    <?php

    class Product extends Trucker
    {
    
    }
The library uses convention over configuration, so it will infer what the URI should be based on your class name.  In the example of 'Product' the URI will be assumed to be */products*.

#### CRUD Operations

Now that you have an ActiveResource class you can use it with CRUD operations as you may expect you would with an ORM.

    $p = Product::find(1);
    $p->name = "My Product";

    if ($p->save()) {
       echo "Saved!";
    } else {
       echo "Error: ". implode("\n", $p->errors());
    }
