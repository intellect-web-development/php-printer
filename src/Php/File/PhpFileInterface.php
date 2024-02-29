<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\File;

use IWD\PhpPrinter\Php\ValueObject\ObjectType;

interface PhpFileInterface
{
    public function getObjectType(): ObjectType;
}
