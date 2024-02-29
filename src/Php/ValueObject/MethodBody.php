<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class MethodBody
{
    public function __construct(
        public string $content,
    ) {
    }
}
