<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class MethodName
{
    public string $value;

    public function __construct(
        string $value
    ) {
        $this->value = $value;
    }
}
