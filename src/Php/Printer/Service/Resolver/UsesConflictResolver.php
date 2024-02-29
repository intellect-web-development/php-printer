<?php

declare(strict_types=1);

namespace IWD\PhpPrinter\Php\Printer\Service\Resolver;

use Exception;
use IWD\PhpPrinter\Php\ValueObject\ObjectType;

class UsesConflictResolver
{
    /**
     * @param ObjectType[] $uses
     *
     * @return ObjectType[]
     *
     * @throws Exception
     */
    public function resolveUsesConflicts(array $uses): array
    {
        return $this->resolveUsesAliases(
            $this->removeUsesDoubles($uses)
        );
    }

    /**
     * @param ObjectType[] $uses
     *
     * @return ObjectType[]
     *
     * @throws Exception
     */
    private function resolveUsesAliases(array $uses): array
    {
        foreach ($uses as $cTypeLeft) {
            foreach ($uses as $cTypeRight) {
                if ($cTypeLeft === $cTypeRight) {
                    continue;
                }
                if ($cTypeLeft->objectName->value === $cTypeRight->objectName->value) {
                    if ($cTypeLeft->alias === $cTypeRight->alias) {
                        $key = array_search($cTypeRight, $uses, true);
                        if (false !== $key) {
                            unset($uses[$key]);
                        }
                        $uses[$cTypeRight->toString()] = new ObjectType(
                            objectName: $cTypeRight->objectName,
                            objectNamespace: $cTypeRight->objectNamespace,
                            alias: $cTypeRight->objectName->value . random_int(1, 100),
                            nullable: $cTypeRight->nullable
                        );

                        return $this->resolveUsesAliases($uses);
                    }
                }
                if (isset($cTypeLeft->alias, $cTypeRight->alias) && $cTypeLeft->alias === $cTypeRight->alias) {
                    $key = array_search($cTypeRight, $uses, true);
                    if (false !== $key) {
                        unset($uses[$key]);
                    }
                    $uses[$cTypeRight->toString()] = new ObjectType(
                        objectName: $cTypeRight->objectName,
                        objectNamespace: $cTypeRight->objectNamespace,
                        alias: $cTypeRight->objectName->value . random_int(1, 100),
                        nullable: $cTypeRight->nullable
                    );

                    return $this->resolveUsesAliases($uses);
                }
            }
        }

        return $uses;
    }

    /**
     * @param ObjectType[] $uses
     *
     * @return ObjectType[]
     */
    private function removeUsesDoubles(array $uses): array
    {
        foreach ($uses as $classTypeLeft) {
            foreach ($uses as $classTypeRight) {
                if ($classTypeLeft === $classTypeRight) {
                    continue;
                }
                if ($classTypeLeft->toString() === $classTypeRight->toString()) {
                    if (null === $classTypeLeft->alias) {
                        $key = array_search($classTypeLeft, $uses, true);
                        if (false !== $key) {
                            unset($uses[$key]);
                        }
                        continue;
                    }
                    if (null === $classTypeRight->alias) {
                        $key = array_search($classTypeRight, $uses, true);
                        if (false !== $key) {
                            unset($uses[$key]);
                        }
                        continue;
                    }

                    $key = array_search($classTypeRight, $uses, true);
                    if (false !== $key) {
                        unset($uses[$key]);
                    }
                    continue;
                }
            }
        }

        $clearedUses = [];
        foreach ($uses as $classType) {
            $clearedUses[$classType->toString()] = $classType;
        }

        return $clearedUses;
    }
}
