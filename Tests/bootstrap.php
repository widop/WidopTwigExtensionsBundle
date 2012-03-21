<?php

system(sprintf('php %s', escapeshellarg(__DIR__.'/bin/vendors')));

require_once __DIR__.'/'.$_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__.'/vendor/twig/lib/Twig/Autoloader.php';


use Symfony\Component\ClassLoader\UniversalClassLoader;

Twig_Autoloader::register();

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array('Symfony'  => __DIR__.'/'.$_SERVER['SYMFONY']));
$loader->register();

spl_autoload_register(function($class)
{
    if(strpos($class, 'Widop\\TwigExtensionsBundle\\') === 0)
    {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 2)).'.php';

        if(!stream_resolve_include_path($path))
            return false;

        require_once $path;
        return true;
    }
});

