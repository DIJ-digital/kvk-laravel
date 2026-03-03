<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/src',
    ])
    ->withImportNames()
    ->withRules([
        DeclareStrictTypesRector::class,
    ])
    ->withSets([
    ])
    ->withPhpSets();
