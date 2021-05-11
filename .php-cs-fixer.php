<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/tests'])
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP74Migration' => true,
        '@PHP74Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,
        'declare_strict_types' => false,
        'native_function_invocation' => ['include' => ['@all']],
        'method_chaining_indentation' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'fopen_flags' => ['b_mode' => true],
    ])
    ->setFinder($finder)
;
