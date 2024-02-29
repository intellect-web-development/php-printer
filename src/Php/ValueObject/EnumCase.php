<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class EnumCase
{
    public function __construct(
        public string $name,
        public ?string $value = null
    ) {
    }
}
