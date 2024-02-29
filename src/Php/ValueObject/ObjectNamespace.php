<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class ObjectNamespace
{
    public function __construct(
        public string $value
    ) {
    }
}
