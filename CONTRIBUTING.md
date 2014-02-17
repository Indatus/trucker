# Trucker Contribution Guide

Thank you for considering contributing to Trucker. Please read the documentation below to determine where and how you should make contributions.

## Contribution Guide Contents

* [Which Branch](#which-branch)
* [Pull Requests](#pull-requests)
  * [Feature Requests](#feature-requests)
* [Coding Guidelines](#coding-guidelines)
* [How To Extend](#how-to-extend)
  * [Request Driver](#request-driver)
  * [Auth Driver](#auth-driver)
  * [Response Interpreter Driver](#response-interpreter-driver)
  * [Error Handler Driver](#error-handler-driver)
  * [Transporter](#transporter)
  * [Collection Query Condition Driver](#collection-query-condition-driver)
  * [Collection Result Order Driver](#collection-result-order-driver)

<a name="which-branch" />
## Which Branch?

**ALL** bug fixes should be made to the versioned branch to which they belong. Bug fixes should never be sent to the `master` branch unless they fix features that exist only in the upcoming release.  The `master` branch will contain features for the next release.  Each versioned release will have a tag with the associated version number.

<a name="pull-requests" />
## Pull Requests

The pull request process differs for new features and bugs. Before sending a pull request for a new feature, you should first create an issue with `[Proposal]` in the title. The proposal should describe the new feature, as well as implementation ideas. The proposal will then be reviewed and either approved or denied. Once a proposal is approved, a pull request may be created implementing the new feature. Pull requests which do not follow this guideline will be closed immediately.

Pull requests for bugs may be sent without creating any proposal issue. If you believe that you know of a solution for a bug that has been filed on Github, please leave a comment detailing your proposed fix.

<a name="feature-requests" />
### Feature Requests

If you have an idea for a new feature you would like to see added to Trucker, you may create an issue on Github with `[Request]` in the title. The feature request will then be reviewed by a core contributor.

<a name="coding-guidelines" />
## Coding Guidelines

Trucker follows the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md), [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standards. In addition to these standards, below is a list of other coding standards that should be followed:

- Interface names are suffixed with `Interface` (`FooInterface`)
- Factory produced drivers must adhere to specific namespacing and naming conventions defined below in [How To Extend](#how-to-extend).

<a name="how-to-extend" />
## How To Extend

Trucker was built to be extended.  Several core areas of the package use Factories and Interfaces to allow for the addition of new functionality simply by adding a new driver to the appropriate namespace and updating the config settings to load it.

For example `AuthFactory::build()` will return an instance that implements `AuthenticationInterface`.  The instance returned will correspond to the driver set in the `driver` setting of the **auth.php** config file.

In order to adhere to the [Open/Closed Principle](http://en.wikipedia.org/wiki/Open/closed_principle) the use of specific namespaces and naming conventions is required and outlined below.

When loaded, the driver name from the config file will be StudlyCased then appended and prepended with the prefix and suffix for the driver.  Lastly the namespace will be added to achieve a fully namespaced class name.  The derived class will be instantiated and returned by the factory.

**Lazy Loading:** Each driver is [lazy loaded](http://en.wikipedia.org/wiki/Lazy_loading). This way if there are drivers in the package that you aren't using, they won't be loaded and impact performance.

<a name="request-driver" />
### Request Driver

The request driver governs how Trucker makes requests that interact with the remote API. Some examples might be REST, SOAP or something API specific like Twitter, Flicker, YouTube etc.

* Production Factory: `Trucker\Facades\RequestFactory`
* Namespace: `Trucker\Requests`
* Class name prefix: `none`
* Class name suffix: `Request`
* Required interface: `Trucker\Requests\RequestableInterface`
* Config file: request.php
* Config key: `driver`
* Example: `Trucker\Requests\RestRequest`

<a name="auth-driver" />
### Auth Driver

The auth driver governs how Trucker authenticates requests to the remote API.  Some examples might be HTTP Basic Authentication, OAuth2, Token Authentication, Query Parameter Signing, or something API specific.

* Production Factory: `Trucker\Facades\AuthFactory`
* Namespace: `Trucker\Requests\Auth`
* Class name prefix: `none`
* Class name suffix: `Authenticator`
* Required interface: `Trucker\Requests\Auth\AuthenticationInterface`
* Config file: auth.php
* Config key: `driver`
* Example: `Trucker\Requests\Auth\BasicAuthenticator`

<a name="response-interpreter-driver" />
### Response Interpreter Driver

The response interpreter driver governs how Trucker interprets responses from the remote API.  Some APIs may use HTTP status codes to indicate success or failure, others may use a particular response parameter etc.

* Production Factory: `Trucker\Facades\ResponseInterpreterFactory`
* Namespace: `Trucker\Responses\Interpreters`
* Class name prefix: `none`
* Class name suffix: `Interpreter`
* Required interface: `Trucker\Responses\Interpreters\ResponseInterpreterInterface`
* Config file: response.php
* Config key: `driver`
* Example: `Trucker\Responses\Interpreters\HttpStatusCodeInterpreter`

<a name="error-handler-driver" />
### Error Handler Driver

The error handler driver governs how Trucker deciphers error messages provided by the remote API.  This isn't to say that the request ended in error, that woudl be the job of the response interpreter.  This driver actually handles specific error messages.

* Production Factory: `Trucker\Facades\ErrorHandlerFactory`
* Namespace: `Trucker\Responses\ErrorHandlers`
* Class name prefix: `none`
* Class name suffix: `ErrorHandler`
* Required interface: `Trucker\Responses\ErrorHandlers\ErrorHandlerInterface`
* Config file: error_handler.php
* Config key: `driver`
* Example: `Trucker\Responses\ErrorHandlers\ArrayResponseErrorHandler`

<a name="transporter" />
### Transporter

The transporter governs the transport language Trucker can expect to receive responses from the remote API. This could be something like JSON, XML, HTML etc.

* Production Factory: `Trucker\Facades\TransporterFactory`
* Namespace: `Trucker\Transporters`
* Class name prefix: `none`
* Class name suffix: `Transporter`
* Required interface: `Trucker\Transporters\TransporterInterface`
* Config file: transporter.php
* Config key: `driver`
* Example: `Trucker\Transporters\JsonTransporter`

<a name="collection-query-condition-driver" />
### Collection Query Condition Driver

The collection query condition driver governs how Trucker will set condition constraints (similar to SQL WHERE conditions) on the request sent to the remote API when fetching a collection of records.

* Production Factory: `Trucker\Facades\QueryConditionFactory`
* Namespace: `Trucker\Finders\Conditions`
* Class name prefix: `none`
* Class name suffix: `QueryCondition`
* Required interface: `Trucker\Finders\Conditions\QueryConditionInterface`
* Config file: query_condition.php
* Config key: `driver`
* Example: `Trucker\Finders\Conditions\GetArrayParamsQueryCondition`

<a name="collection-result-order-driver" />
### Collection Result Order Driver

The collection result order driver governs how Trucker will set the collection ordering requirements on the request sent to the remote API when fetching a collection of records.

* Production Factory: `Trucker\Facades\QueryResultOrderFactory`
* Namespace: `Trucker\Finders\Conditions`
* Class name prefix: `none`
* Class name suffix: `ResultOrder`
* Required interface: `Trucker\Finders\Conditions\QueryResultOrderInterface`
* Config file: result_order.php
* Config key: `driver`
* Example: `Trucker\Finders\Conditions\GetParamsResultOrder`
