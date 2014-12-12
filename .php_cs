<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/app', __DIR__ . '/src')
;

return Symfony\CS\Config\Config::create()
    ->fixers(array('concat_with_spaces', '-concat_without_spaces'))
    ->finder($finder)
;
