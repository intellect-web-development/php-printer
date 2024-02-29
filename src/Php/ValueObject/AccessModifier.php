<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

enum AccessModifier: string
{
    case Public = 'public';
    case Protected = 'protected';
    case Private = 'private';
}
