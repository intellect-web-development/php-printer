<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Constant
{
    public function __construct(
        public mixed $value,
        public AccessModifier $accessModifier,
        public string $name,
        public bool $shieldValue = true,
    ) {
    }
}
