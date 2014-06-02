# Installation

## Symfony ~2.3

### Add the WidopTwigExtensionsBundle to your composer configuration

Add the bundle to the require section of your `composer.json`

``` json
{
    "require": {
        "widop/twig-extensions-bundle": "~2.0"
    }
}
```

Run the composer update command

``` bash
$ php composer.phar update
```

### Add the WidopTwigExtensionsBundle to your application kernel

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        //..
        new Widop\TwigExtensionsBundle\WidopTwigExtensionsBundle(),
    );
}
```
