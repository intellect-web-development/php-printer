<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

interface TypeInterface
{
    public function toString(): string;

    public function isNullable(): bool;

    public function setNullable(bool $nullable): void;
}
