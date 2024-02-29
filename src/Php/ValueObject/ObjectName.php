<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class ObjectName
{
    public function __construct(
        public string $value
    ) {
    }
}
