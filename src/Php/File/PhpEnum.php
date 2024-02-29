<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\File;

use IWD\PhpPrinter\Contract\File;
use IWD\PhpPrinter\Php\ValueObject\Attribute;
use IWD\PhpPrinter\Php\ValueObject\ObjectName;
use IWD\PhpPrinter\Php\ValueObject\ObjectNamespace;
use IWD\PhpPrinter\Php\ValueObject\ObjectType;
use IWD\PhpPrinter\Php\ValueObject\Comment;
use IWD\PhpPrinter\Php\ValueObject\Constant;
use IWD\PhpPrinter\Php\ValueObject\EnumCase;
use IWD\PhpPrinter\Php\ValueObject\Method;
use IWD\PhpPrinter\Php\ValueObject\Property;
use IWD\PhpPrinter\Php\ValueObject\TypeInterface;

class PhpEnum extends File implements PhpFileInterface
{
    /**
     * @param Constant[]|null   $constants
     * @param ObjectType[]|null $traits
     * @param ObjectType[]|null $uses
     * @param ObjectType[]|null $implementations
     * @param Attribute[]|null  $attributes
     * @param Property[]|null   $properties
     * @param Method[]|null     $methods
     * @param EnumCase[]|null   $cases
     */
    public function __construct(
        string $path,
        string $name,
        public ObjectType $objectType,
        public ObjectName $objectName,
        public ?ObjectNamespace $objectNamespace,
        public ?Comment $comment,
        public ?ObjectType $extends,
        public ?TypeInterface $returnType,
        public ?array $constants,
        public ?array $traits,
        public ?array $uses,
        public ?array $implementations,
        public ?array $attributes,
        public ?array $properties,
        public ?array $methods,
        public ?array $cases,
        public bool $final,
        public bool $abstract,
        public bool $strictTypes,
    ) {
        parent::__construct(
            path: $path,
            name: $name,
        );
    }

    public function getObjectType(): ObjectType
    {
        return $this->objectType;
    }
}
