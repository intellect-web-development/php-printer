<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class NativeType implements TypeInterface
{
    public const BOOLEAN = 'bool';
    public const INTEGER = 'int';
    public const FLOAT = 'float';
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const OBJECT = 'object';
    public const RESOURCE = 'resource';
    public const NULL = 'null';
    public const CALLABLE = 'callable';
    public const VOID = 'void';
    public const MIXED = 'mixed';
    public const SELF = 'self';
    public const STATIC = 'static';

    public const NATIVE_TYPES = [
        self::BOOLEAN,
        self::INTEGER,
        self::FLOAT,
        self::STRING,
        self::ARRAY,
        self::OBJECT,
        self::RESOURCE,
        self::NULL,
        self::CALLABLE,
        self::VOID,
        self::MIXED,
        self::SELF,
        self::STATIC,
    ];

    public function __construct(
        public string $name,
        public bool $nullable,
    ) {
    }

    public static function isNative(string $type): bool
    {
        return in_array($type, self::NATIVE_TYPES);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public static function boolean(bool $nullable = false): self
    {
        return new self(self::BOOLEAN, $nullable);
    }

    public static function integer(bool $nullable = false): self
    {
        return new self(self::INTEGER, $nullable);
    }

    public static function float(bool $nullable = false): self
    {
        return new self(self::FLOAT, $nullable);
    }

    public static function string(bool $nullable = false): self
    {
        return new self(self::STRING, $nullable);
    }

    public static function array(bool $nullable = false): self
    {
        return new self(self::ARRAY, $nullable);
    }

    public static function void(bool $nullable = false): self
    {
        return new self(self::VOID, $nullable);
    }

    public static function object(bool $nullable = false): self
    {
        return new self(self::OBJECT, $nullable);
    }

    public static function resource(bool $nullable = false): self
    {
        return new self(self::RESOURCE, $nullable);
    }

    public static function null(bool $nullable = false): self
    {
        return new self(self::NULL, $nullable);
    }

    public static function callable(bool $nullable = false): self
    {
        return new self(self::CALLABLE, $nullable);
    }

    public static function mixed(): self
    {
        return new self(self::MIXED, false);
    }

    public static function self(bool $nullable = false): self
    {
        return new self(self::SELF, $nullable);
    }

    public static function static(bool $nullable = false): self
    {
        return new self(self::STATIC, $nullable);
    }

    public function setNullable(bool $nullable): void
    {
        $this->nullable = $nullable;
    }
}
