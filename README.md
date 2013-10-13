yii-sentry
==========

[![Latest Stable Version](https://poser.pugx.org/crisu83/yii-sentry/v/stable.png)](https://packagist.org/packages/crisu83/yii-sentry)

Sentry for the Yii PHP framework.

yii-sentry is an extension for Yii that allows for sending data to [Sentry](http://getsentry.com). 
It comes with an application component that allows for centralized access to the Raven client,
an error handler that sends errors and exception to Sentry and a log route that sends log messages to Sentry.
It has never been this easy to professionally manage your errors.

## Features

* Application component for easy access to the Raven client
* Error handler that sends errors to Sentry
* Log route that sends messages to Sentry

## Resources

* [Sentry](http://getsentry.com)
* [Sentry docs](http://sentry.readthedocs.org/en/latest/)
* [Raven project](http://github.com/getsentry/raven-php)

## Setup

The easiest way to install this extension is to use [Composer](http://getcomposer.org) by adding the following to your composer.json file:

```js
  "require": {
    "crisu83/yii-sentry": "<replace-with-latest-version>"
  }
```

Run the following command in the root directory of your project to install the extension:

```bash
php composer.phar install
```

> TIP: Create a path alias to Composer's vendor directory called **vendor** to ease class mapping to dependencies by adding it to ```aliases``` in your application configuration.

If you do not want to use Composer, you can download the extension and its dependencies and set everything up manually.

Once you have downloaded the extension add the following to your application configuration:

```php
  'components' => array(
    'sentry' => array(
      'class' => 'vendor.crisu83.yii-sentry.components.SentryClient',
      'dns' => '<replace-with-your-sentry-dns>'
    ),
  ),
```

The following configuration parameters are available for the SentryClient:

* **dns**: (string) dns to use when connecting to Sentry
* **projectId**: (int) Sentry project id (defaults to 1)
* **environment**: (string) name of the active environment
* **enabledEnvironments**: (array) list of names for environments in which data will be sent to Sentry
* **options**: (array) options to pass to the Raven client with the following structure:
  * **logger**: (string) name of the logger
  * **auto_log_stacks**: (bool) whether to automatically log stacktraces
  * **name**: (string) name of the server
  * **site**: (string) name of the installation
  * **tags**: (array) key/value pairs that describe the event
  * **trace**: (bool) whether to send stacktraces
  * **timeout**: (int) timeout when connecting to Sentry (in seconds)
  * **exclude**: (array) class names of exceptions to exclude
  * **shift_vars**: (bool) whether to shift variables when creating a backtrace
  * **processors**: (array) list of data processors
                                    
## Sending errors to Sentry

To enable the SentryErrorHandler add the following to your application configuration:

```php
  'components' => array(
    'errorHandler' => array(
      'class' => 'vendor.crisu83.yii-sentry.components.SentryErrorHandler',
    ),
  ),
```

The following configuration parameters are available for the SentryErrorHandler:

* **sentryClientID**: (string) component ID for the sentry client

That's it, now errors and exceptions will be sent to Sentry.

## Sending log messages to Sentry

To enable the SentryLogRoute add the following to your application configuration:

```php
  'components' => array(
    'log' => array(
      'class' => 'CLogRouter',
      'routes' => array(
        array(
          'class' => 'vendor.crisu83.yii-sentry.components.SentryLogRoute',
          'levels' => 'error, warning',
        ),
      ),
    ),
  ),
```

The following configuration parameters are available for the SentryLogRoute:

* **sentryClientID**: (string) component ID for the sentry client

That's it, now log messages with levels **error** and **warning** will be sent to Sentry.

> TIP: Do not log messages with level **trace** to Sentry because it will slow down your application a lot.