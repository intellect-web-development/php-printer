<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Contract;

// todo: этот файл потом вынесу в отдельную библиотеку IWD\FilePrinterContracts
abstract class File
{
    public function __construct(
        public string $path,
        public string $name,
    ) {
    }
}
