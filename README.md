# FcPhp Dispach

Package to dispach request to controller. Uses [FcPhp Di](https://github.com/00f100/fcphp-di) to find Controller instance.

[![Build Status](https://travis-ci.org/00F100/fcphp-dispach.svg?branch=master)](https://travis-ci.org/00F100/fcphp-dispach) [![codecov](https://codecov.io/gh/00F100/fcphp-dispach/branch/master/graph/badge.svg)](https://codecov.io/gh/00F100/fcphp-dispach)

[![PHP Version](https://img.shields.io/packagist/php-v/00f100/fcphp-dispach.svg)](https://packagist.org/packages/00F100/fcphp-dispach) [![Packagist Version](https://img.shields.io/packagist/v/00f100/fcphp-dispach.svg)](https://packagist.org/packages/00F100/fcphp-dispach) [![Total Downloads](https://poser.pugx.org/00F100/fcphp-dispach/downloads)](https://packagist.org/packages/00F100/fcphp-dispach)

## How to install

Composer:
```sh
$ composer require 00f100/fcphp-dispach
```

or add in composer.json
```json
{
    "require": {
        "00f100/fcphp-dispach": "*"
    }
}
```

## How to use

### Configure Dependency Injection with [FcPhp Di](https://github.com/00f100/fcphp-di)

```php

use FcPhp\Di\Facades\DiFacade;
use FcPhp\Controller\Controller;

// Class example ...
class ExampleController extends Controller
{
    public function findAll($foo, $bar)
    {
        return compact('foo', 'bar');
    }
}
// Configure class into FcPhp Di
$di = DiFacade::getInstance();
$di->set('ExampleController', 'ExampleController');

```

### Get instance and run Dispach

```php

use FcPhp\Dispach\Facades\DispachFacade;
// Init Dispach
$instance = DispachFacade::getInstance();

/*
    Return ExampleController->findAll('foo_value', 'bar_value'):
    Array (
        'foo' => 'foo_value',
        'bar' => 'bar_value'
    )
*/
print_r($instance->dispach('ExampleController@findAll', ['foo_value', 'bar_value']));
```