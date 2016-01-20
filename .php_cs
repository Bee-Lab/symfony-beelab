<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([__DIR__.'/app', __DIR__.'/src', __DIR__.'/tests'])
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->fixers(['-psr0', 'ordered_use', 'short_array_syntax'])
    ->finder($finder)
;
