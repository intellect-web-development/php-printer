<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Enum;

use IWD\PhpPrinter\Contract\File;
use IWD\PhpPrinter\Php\File\PhpEnum;
use IWD\PhpPrinter\Php\Printer\Service\Helper;
use IWD\PhpPrinter\Php\Printer\Service\Resolver\UsesConflictResolver;
use IWD\PhpPrinter\Php\ValueObject\ObjectType;
use Throwable;

readonly class Printer
{
    public function __construct(
        private Helper $helper,
        private UsesConflictResolver $usesConflictResolver,
    ) {
    }

    public function isSupport(File $file): bool
    {
        return $file instanceof PhpEnum;
    }

    /**
     * @throws Throwable
     */
    public function print(File $file): string
    {
        /** @var PhpEnum $file */
        $uses = $this->usesConflictResolver->resolveUsesConflicts(
            self::extractUses($file),
        );
        ksort($uses);

        return $this->helper->renderEnumFile($file, $uses);
    }

    /**
     * @return ObjectType[]
     */
    public static function extractUses(PhpEnum $file): array
    {
        $uses = [];
        if (null !== $file->uses) {
            foreach ($file->uses as $classType) {
                $uses[] = $classType;
            }
        }
        if (null !== $file->extends) {
            $uses[] = $file->extends;
        }
        if (null !== $file->attributes) {
            foreach ($file->attributes as $attribute) {
                $uses[] = $attribute->objectType;
            }
        }
        if (null !== $file->implementations) {
            foreach ($file->implementations as $implementation) {
                $uses[] = $implementation;
            }
        }
        if (null !== $file->traits) {
            foreach ($file->traits as $trait) {
                $uses[] = $trait;
            }
        }
        if (null !== $file->properties) {
            foreach ($file->properties as $property) {
                if ($property->type instanceof ObjectType) {
                    $uses[] = $property->type;
                }
                foreach ($property->attributes ?? [] as $attribute) {
                    $uses[] = $attribute->objectType;
                }
            }
        }
        if (null !== $file->methods) {
            foreach ($file->methods as $method) {
                if ($method->returnType instanceof ObjectType) {
                    $uses[] = $method->returnType;
                }
                foreach ($method->attributes ?? [] as $attribute) {
                    $uses[] = $attribute->objectType;
                }
                foreach ($method->parameters ?? [] as $parameter) {
                    if ($parameter->type instanceof ObjectType) {
                        $uses[] = $parameter->type;
                    }
                }
            }
        }
        if ($file->returnType instanceof ObjectType) {
            $uses[] = $file->returnType;
        }

        return $uses;
    }
}
