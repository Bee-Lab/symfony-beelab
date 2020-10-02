<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src', __DIR__.'/tests'])
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHP73Migration' => true,
        '@PHPUnit75Migration:risky' => true,
        'list_syntax' => ['syntax' => 'short'],
        'declare_strict_types' => false,
        'native_function_invocation' => true,
        'method_chaining_indentation' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'fopen_flags' => ['b_mode' => true],
    ])
    ->setFinder($finder)
;
