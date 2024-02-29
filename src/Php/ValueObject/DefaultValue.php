<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class DefaultValue
{
    public function __construct(
        public bool $isset,
        public mixed $value = null,
        public bool $shieldValue = true,
    ) {
    }

    public static function stub(): self
    {
        return new self(false);
    }

    public static function set(mixed $value): self
    {
        return new self(true, $value);
    }
}
