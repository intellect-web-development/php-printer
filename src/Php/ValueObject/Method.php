<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\ValueObject;

class Method
{
    /**
     * @param Attribute[]|null $attributes
     * @param Parameter[]|null $parameters
     */
    public function __construct(
        public MethodName $name,
        public ?MethodBody $body,
        public AccessModifier $accessModifier,
        public bool $static = false,
        public ?TypeInterface $returnType = null,
        public ?Comment $comment = null,
        public ?array $attributes = [],
        public ?array $parameters = [],
        public bool $abstract = false,
        public bool $final = false,
    ) {
    }

    /**
     * @return Parameter[]
     */
    public function uniqueParameters(): array
    {
        $uniqueParameters = [];
        foreach ($this->parameters ?? [] as $parameter) {
            $uniqueParameters[$parameter->type?->toString() . $parameter->name] = $parameter;
        }

        return array_values($uniqueParameters);
    }
}
