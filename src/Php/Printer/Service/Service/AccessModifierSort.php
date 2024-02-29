<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Service;

use IWD\PhpPrinter\Php\ValueObject\AccessModifier;

class AccessModifierSort
{
    /**
     * @template T
     *
     * @param T[]|array|null $elements
     *
     * @return T[]
     */
    public function sortElementsByAccessModifier(
        ?array $elements,
    ): array {
        if (null === $elements) {
            return [];
        }
        $propertyName = 'accessModifier';

        $public = [];
        $protected = [];
        $private = [];
        foreach ($elements as $element) {
            if (AccessModifier::Public === $element->$propertyName) {
                $public[] = $element;
            }
            if (AccessModifier::Protected === $element->$propertyName) {
                $protected[] = $element;
            }
            if (AccessModifier::Private === $element->$propertyName) {
                $private[] = $element;
            }
        }

        return [...$public, ...$protected, ...$private];
    }
}
