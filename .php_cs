<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([__DIR__.'/app', __DIR__.'/src'])
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->fixers(['ordered_use', 'short_array_syntax'])
    ->finder($finder)
;
