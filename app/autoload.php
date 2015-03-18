<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

/*
 * @var \Composer\Autoload\ClassLoader
 */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
