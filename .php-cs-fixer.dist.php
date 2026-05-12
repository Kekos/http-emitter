<?php declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS' => true,
        'fully_qualified_strict_types' => [
            'import_symbols' => true,
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'native_constant_invocation' => true,
        'native_function_invocation' => [
            'exclude' => [
                'headers_sent',
                'header',
            ],
            'include' => [
                '@all',
            ],
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
