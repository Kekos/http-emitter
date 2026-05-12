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
    ])
    ->setFinder($finder)
;
