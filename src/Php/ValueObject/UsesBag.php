<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class UsesBag
{
    /**
     * @var ObjectType[]
     */
    private array $uses = [];

    public function add(ObjectType $objectType): void
    {
        $this->uses[] = $objectType;
    }

    public function getAll(): array
    {
        return $this->uses;
    }
}
