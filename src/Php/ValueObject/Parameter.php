<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Parameter
{
    public DefaultValue $defaultValue;

    public function __construct(
        public string $name,
        public bool $nullable,
        public ?TypeInterface $type = null,
        DefaultValue $defaultValue = null,
        public bool $readonly = false,
        public ?AccessModifier $accessModifier = null,
    ) {
        $this->defaultValue = $defaultValue ?? DefaultValue::stub();
    }
}
