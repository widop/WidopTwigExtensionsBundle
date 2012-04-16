<?php

/*
 * This file is part of the Widop package.
 *
 * (c) Widop <contact@widop.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/'.$_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/twig/lib/Twig/Autoloader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

Twig_Autoloader::register();

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Symfony'  => __DIR__.'/'.$_SERVER['SYMFONY']));
$loader->register();

spl_autoload_register(function($class)
{
    if (strpos($class, 'Widop\\TwigExtensionsBundle\\') === 0) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 2)).'.php';

        if (!stream_resolve_include_path($path)) {
            return false;
        }

        require_once $path;

        return true;
    }
});
