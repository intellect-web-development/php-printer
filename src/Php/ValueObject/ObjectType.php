<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class ObjectType implements TypeInterface
{
    public function __construct(
        public ObjectName $objectName,
        public ?ObjectNamespace $objectNamespace,
        public ?string $alias,
        public bool $nullable
    ) {
    }

    public static function fromString(
        string $classString,
        string $alias = null,
        bool $nullable = false
    ): self {
        $explodeClassString = explode('\\', $classString);

        return new self(
            objectName: new ObjectName(array_pop($explodeClassString)),
            objectNamespace: new ObjectNamespace(implode('\\', $explodeClassString)),
            alias: $alias,
            nullable: $nullable
        );
    }

    public function toString(): string
    {
        return isset($this->objectNamespace) && '' !== $this->objectNamespace->value
            ? "{$this->objectNamespace->value}\\{$this->objectName->value}"
            : $this->objectName->value
        ;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }
}
