<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Property
{
    public DefaultValue $defaultValue;

    /**
     * @param Attribute[]|null $attributes
     */
    public function __construct(
        public PropertyName $name,
        public ?TypeInterface $type,
        public AccessModifier $accessModifier,
        public ?array $attributes = [],
        public bool $nullable = false,
        public bool $static = false,
        public bool $readonly = false,
        public ?Comment $comment = null,
        DefaultValue $defaultValue = null
    ) {
        $this->defaultValue = $defaultValue ?? DefaultValue::stub();
        $this->type?->setNullable($this->nullable);
    }
}
