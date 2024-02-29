<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Renderers;

class Shielder
{
    public function shieldValue(mixed $value): string
    {
        if (is_string($value)) {
            return "'" . $value . "'";
        }
        $payload = json_encode($value, JSON_THROW_ON_ERROR);
        $payload = str_replace(',', ', ', $payload);
        if (is_array($value)) {
            $map = [
                '{' => '[',
                '}' => ']',
                ':' => ' => ',
                '"' => "'",
            ];

            return str_replace(array_keys($map), array_values($map), $payload);
        }

        return $payload;
    }
}
