<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use ArrayAccess;
use ReflectionException;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IntegrationTestCase extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::bootKernel();
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @template T
     *
     * @param class-string<T> $id
     *
     * @return T
     * @throws \Exception
     */
    public static function get(string $id)
    {
        /** @var T $instance */
        $instance = parent::getContainer()->get($id);

        return $instance;
    }

    protected static function bindMock(object $object, string $property, mixed $value): void
    {
        $className = $object::class;
        try {
            $refProperty = self::getReflectionProperty($className, $property);
            $refProperty->setValue($object, $value);
        } catch (ReflectionException $reflectionException) {
            if ($object instanceof ArrayAccess) {
                $object[$property] = $value;
            } else {
                throw $reflectionException;
            }
        }
    }

    /**
     * @throws ReflectionException
     */
    private static function getReflectionProperty(string $className, string $property): ReflectionProperty
    {
        $refProperty = new ReflectionProperty($className, $property);
        $refProperty->setAccessible(true);

        return $refProperty;
    }
}
