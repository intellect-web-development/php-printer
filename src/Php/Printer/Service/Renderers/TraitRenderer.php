<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Renderers;

use IWD\PhpPrinter\Php\Printer\Service\Dictionary;
use IWD\PhpPrinter\Php\Printer\Service\Resolver\ClassNameResolver;
use IWD\PhpPrinter\Php\ValueObject\ObjectType;

readonly class TraitRenderer
{
    public function __construct(
        private ClassNameResolver $classNameResolver,
    ) {
    }

    /**
     * @param ObjectType[]|null $traits
     */
    public function renderTraitsBlock(array|null $traits): ?string
    {
        return null !== $traits && [] !== $traits
            ? implode(
                PHP_EOL,
                array_map(
                    function (ObjectType $trait) {
                        return Dictionary::TAB . $this->renderTrait($trait);
                    },
                    $traits
                )
            )
            : null;
    }

    public function renderTrait(ObjectType $trait): string
    {
        return 'use ' . $this->classNameResolver->resolve($trait) . ';';
    }
}
