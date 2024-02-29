<?php

namespace App\Tests\Integration\Printer\Implementation\Php\Service\Renderers;

use App\Tests\Integration\IntegrationTestCase;
use Generator;
use IWD\PhpPrinter\Php\Printer\Service\Renderers\ConstantRenderer;
use IWD\PhpPrinter\Php\ValueObject\AccessModifier;
use IWD\PhpPrinter\Php\ValueObject\Constant;

// todo: это просто пример как можно на запчасти принтера писать тесты
class ConstantRendererTest extends IntegrationTestCase
{
    protected static ConstantRenderer $constantRenderer;

    public function setUp(): void
    {
        self::$constantRenderer = self::get(ConstantRenderer::class);
    }

    public function testRenderConstantBlock(): void
    {
        $this->markTestSkipped('Need create test ConstantRendererTest.testRenderConstantBlock');
    }

    /**
     * @dataProvider renderConstantDataProvider
     */
    public function testRenderConstant(Constant $constant, string $expected): void
    {
        self::assertEquals(
            $expected,
            self::$constantRenderer->renderConstant($constant)
        );
    }

    public static function renderConstantDataProvider(): Generator
    {
        yield [
            'constant' => new Constant(
                value: '123',
                accessModifier: AccessModifier::Private,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => 'private const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: '123',
                accessModifier: AccessModifier::Protected,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => 'protected const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: '123',
                accessModifier: AccessModifier::Public,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => 'public const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: '123',
                accessModifier: AccessModifier::Private,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => "private const MY_CONST = '123';",
        ];
        yield [
            'constant' => new Constant(
                value: '123',
                accessModifier: AccessModifier::Protected,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => "protected const MY_CONST = '123';",
        ];
        yield [
            'constant' => new Constant(
                value: '123',
                accessModifier: AccessModifier::Public,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => "public const MY_CONST = '123';",
        ];

        yield [
            'constant' => new Constant(
                value: 123,
                accessModifier: AccessModifier::Private,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => 'private const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: 123,
                accessModifier: AccessModifier::Protected,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => 'protected const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: 123,
                accessModifier: AccessModifier::Public,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => 'public const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: 123,
                accessModifier: AccessModifier::Private,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => 'private const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: 123,
                accessModifier: AccessModifier::Protected,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => 'protected const MY_CONST = 123;',
        ];
        yield [
            'constant' => new Constant(
                value: 123,
                accessModifier: AccessModifier::Public,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => 'public const MY_CONST = 123;',
        ];

        yield [
            'constant' => new Constant(
                value: ['my', 'array'],
                accessModifier: AccessModifier::Private,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => "private const MY_CONST = ['my', 'array'];",
        ];
        yield [
            'constant' => new Constant(
                value: ['my', 'array'],
                accessModifier: AccessModifier::Protected,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => "protected const MY_CONST = ['my', 'array'];",
        ];
        yield [
            'constant' => new Constant(
                value: ['my', 'array'],
                accessModifier: AccessModifier::Public,
                name: 'MY_CONST',
                shieldValue: false,
            ),
            'expected' => "public const MY_CONST = ['my', 'array'];",
        ];
        yield [
            'constant' => new Constant(
                value: ['my', 'array'],
                accessModifier: AccessModifier::Private,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => "private const MY_CONST = ['my', 'array'];",
        ];
        yield [
            'constant' => new Constant(
                value: ['my', 'array'],
                accessModifier: AccessModifier::Protected,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => "protected const MY_CONST = ['my', 'array'];",
        ];
        yield [
            'constant' => new Constant(
                value: ['my', 'array'],
                accessModifier: AccessModifier::Public,
                name: 'MY_CONST',
                shieldValue: true,
            ),
            'expected' => "public const MY_CONST = ['my', 'array'];",
        ];
    }
}
