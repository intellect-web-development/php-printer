<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Comment
{
    public function __construct(
        public string $content,
    ) {
    }
}
