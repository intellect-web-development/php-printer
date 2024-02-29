<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Renderers;

use IWD\PhpPrinter\Php\Printer\Service\Dictionary;
use IWD\PhpPrinter\Php\ValueObject\Constant;

readonly class ConstantRenderer
{
    public function __construct(
        private Shielder $shielder,
    ) {
    }

    /**
     * @param Constant[]|null $constants
     */
    public function renderConstantBlock(array|null $constants): ?string
    {
        return null !== $constants && [] !== $constants
            ? implode(
                PHP_EOL,
                array_map(
                    function (Constant $constant) {
                        return Dictionary::TAB . $this->renderConstant($constant);
                    },
                    $constants
                )
            )
            : null;
    }

    // todo: можно рефакторить, тесты есть
    public function renderConstant(Constant $constant): ?string
    {
        $result = $constant->accessModifier->value . ' const ' . $constant->name;
        if (null !== $constant->value) {
            if (is_array($constant->value)) {
                $result .= ' = ' . $this->shielder->shieldValue($constant->value);
            } else {
                $result .= ' = ' . ($constant->shieldValue ? $this->shielder->shieldValue($constant->value) : $constant->value);
            }
        }

        return $result . ';';
    }
}
