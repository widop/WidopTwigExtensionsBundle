# Installation

## Symfony 2.1.*

### Add the WidopTwigExtensionsBundle to your composer configuration

Add the bundle to the require section of your `composer.json`

``` json
{
    "require": {
        "widop/twig-extensions-bundle": "dev-master"
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

## Symfony 2.0.*

### Add the WidopTwigExtensionsBundle to your vendor/bundles/ directory

#### Using the vendors script

Add the following lines in your ``deps`` file

```
[WidopTwigExtensionsBundle]
    git=http://github.com/widop/WidopTwigExtensionsBundle.git
    target=bundles/Widop/TwigExtensionsBundle
```

Run the vendors script

``` bash
$ php bin/vendors update
```

#### Using submodules

``` bash
$ git submodule add http://github.com/widop/WidopTwigExtensionsBundle.git vendor/bundles/Widop/TwigExtensionsBundle
```

### Add the Widop namespace to your autoloader

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    //..
    'Widop' => __DIR__.'/../vendor/bundles',
);
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
