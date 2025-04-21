<?php

declare(strict_types=1);

use Rector\TypeDeclaration\Rector\ClassMethod\StringReturnTypeFromStrictStringReturnsRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return \Rector\Config\RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
    ])
    ->withRules([
        StringReturnTypeFromStrictStringReturnsRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        TypedPropertyFromAssignsRector::class,
    ]);
