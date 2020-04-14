<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'concat_space' => ['spacing' => 'one'],
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'array_indentation' => true,
        'php_unit_construct' => true,
        'php_unit_strict' => true,
        'blank_line_before_statement' => ['statements' => ['break', 'continue', 'declare', 'throw', 'try']],
        'method_chaining_indentation' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(__DIR__)
    )
;
