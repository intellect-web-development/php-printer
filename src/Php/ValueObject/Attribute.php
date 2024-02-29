<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Attribute
{
    /**
     * @param Argument[]|null $arguments
     */
    public function __construct(
        public ObjectType $objectType,
        public ?array $arguments = []
    ) {
    }
}
