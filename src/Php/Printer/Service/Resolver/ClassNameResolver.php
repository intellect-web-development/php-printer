<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Resolver;

use IWD\PhpPrinter\Php\ValueObject\ObjectType;

class ClassNameResolver
{
    public function resolve(ObjectType $classType): string
    {
        return $classType->alias ?? $classType->objectName->value;
    }
}
