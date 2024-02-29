<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Renderers;

use IWD\PhpPrinter\Php\ValueObject\ObjectType;

readonly class UsesRenderer
{
    public function renderUse(ObjectType $use): string
    {
        if (null === $use->alias) {
            return "use {$use->toString()};";
        }

        return "use {$use->toString()} as {$use->alias};";
    }

    /**
     * @param ObjectType[]|null $objectTypes
     */
    public function renderUsesBlock(array|null $objectTypes, ObjectType $selfObjectType): ?string
    {
        return null !== $objectTypes && [] !== $objectTypes
            ? implode(
                PHP_EOL,
                array_filter(
                    array_map(
                        function (ObjectType $use) use ($selfObjectType) {
                            if ($selfObjectType->objectNamespace?->value === $use->objectNamespace?->value) {
                                return null;
                            }

                            return $this->renderUse($use);
                        },
                        $objectTypes
                    )
                )
            ) . PHP_EOL
            : null;
    }
}
