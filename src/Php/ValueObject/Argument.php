<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Argument
{
    public function __construct(
        public string $name,
        public mixed $value,
        public bool $shieldValue = true,
    ) {
    }
}
