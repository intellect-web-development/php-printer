<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Renderers;

use IWD\PhpPrinter\Php\Printer\Service\Dictionary;
use IWD\PhpPrinter\Php\ValueObject\EnumCase;

readonly class EnumCaseRenderer
{
    public function __construct(
        private Shielder $shielder,
    ) {
    }

    /**
     * @param EnumCase[]|null $cases
     */
    public function renderCaseBlock(array|null $cases): ?string
    {
        return null !== $cases && [] !== $cases
            ? implode(
                PHP_EOL,
                array_map(
                    function (EnumCase $case) {
                        return $this->renderCase($case);
                    },
                    $cases
                )
            )
            : null;
    }

    public function renderCase(EnumCase $case): string
    {
        $result = Dictionary::TAB . 'case ' . $case->name;
        if ($case->value) {
            $result .= ' = ' . $this->shielder->shieldValue($case->value);
        }
        $result .= ';';

        return $result;
    }
}
