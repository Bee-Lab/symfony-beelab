<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = new PhpCsFixer\Finder()
    ->in([__DIR__.'/src', __DIR__.'/tests'])
;

return new PhpCsFixer\Config()
    ->setCacheFile(__DIR__.'/var/cache/.php-cs-fixer.cache')
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP8x5Migration' => true,
        '@PHP8x5Migration:risky' => true,
        '@PHPUnit11x0Migration:risky' => true,
        'declare_strict_types' => false,
        'native_function_invocation' => ['include' => ['@internal']],
        'method_chaining_indentation' => true,
        'fopen_flags' => ['b_mode' => true],
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self', 'methods' => ['once' => 'this']],
        'trailing_comma_in_multiline' => ['elements' => ['arguments', 'arrays', 'match', 'parameters']],
    ])
    ->setFinder($finder)
;
