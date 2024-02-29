<?php

namespace IWD\PhpPrinter\Php\Printer\Service;

use IWD\PhpPrinter\Php\File\PhpClass;
use IWD\PhpPrinter\Php\File\PhpInterface;
use IWD\PhpPrinter\Php\Printer\Service\Renderers\ConstantRenderer;
use IWD\PhpPrinter\Php\Printer\Service\Renderers\EnumCaseRenderer;
use IWD\PhpPrinter\Php\Printer\Service\Renderers\Shielder;
use IWD\PhpPrinter\Php\Printer\Service\Renderers\TraitRenderer;
use IWD\PhpPrinter\Php\Printer\Service\Renderers\UsesRenderer;
use IWD\PhpPrinter\Php\Printer\Service\Resolver\ClassNameResolver;
use IWD\PhpPrinter\Php\Printer\Service\Service\AccessModifierSort;
use IWD\PhpPrinter\Php\ValueObject\Attribute;
use IWD\PhpPrinter\Php\ValueObject\Comment;
use IWD\PhpPrinter\Php\ValueObject\Method;
use IWD\PhpPrinter\Php\ValueObject\NativeType;
use IWD\PhpPrinter\Php\ValueObject\ObjectName;
use IWD\PhpPrinter\Php\ValueObject\ObjectNamespace;
use IWD\PhpPrinter\Php\ValueObject\ObjectType;
use IWD\PhpPrinter\Php\ValueObject\Parameter;
use IWD\PhpPrinter\Php\ValueObject\Property;
use IWD\PhpPrinter\Php\ValueObject\TypeInterface;
use IWD\Templator\Dto\Renderable;
use IWD\Templator\Service\Renderer;

//todo: реализация этого класса была портирована и адаптирована, много быдло-кода, но работает вроде стабильно.
// Нужно отрефакторить
// - выделить методы, которые что-то рендерят в сервисы, и использовать их.
// - как только методы закончатся перенести методы типо renderClassFile туда где они вызываются
// - переписать сервисы рендеринга на более понятный код с использованием темплейт-рендерера
readonly class Helper
{
    public function __construct(
        private Renderer $renderer,
        private ClassNameResolver $classNameResolver,
        private TraitRenderer $traitRenderer,
        private UsesRenderer $usesRenderer,
        private EnumCaseRenderer $enumCaseRenderer,
        private ConstantRenderer $constantRenderer,
        private Shielder $shielder,
        private AccessModifierSort $accessModifierSort,
    ) {
    }

    /**
     * @param ObjectType[] $uses
     *
     * @throws \JsonException
     */
    public function renderClassFile(PhpClass $file, array $uses): string
    {
        $content = array_filter([
            'class_traits' => $this->traitRenderer->renderTraitsBlock($file->traits),
            'class_constants' => $this->constantRenderer->renderConstantBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->constants)
            ),
            'class_properties' => $this->renderPropertyBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->properties),
                $uses
            ),
            'class_methods' => $this->renderMethodBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->methods),
                $file->objectNamespace,
                $uses
            ),
        ]);

        return $this->renderer->render(
            new Renderable(
                template: self::template(),
                variables: [
                    'php_eol' => PHP_EOL,
                    'class_out' => implode(
                        PHP_EOL,
                        array_filter([
                            'strictTypes' => $this->renderStrictTypes($file->strictTypes),
                            'namespace' => $this->renderNamespace($file->objectNamespace),
                            'useBlock' => $this->usesRenderer->renderUsesBlock($uses, $file->objectType),
                        ])
                    ),
                    'class_doc' => PHP_EOL . implode(
                        '',
                        array_filter([
                                'class_comment' => (function () use ($file) {
                                    $comment = $this->renderComment($file->comment);
                                    if (null === $comment) {
                                        return '';
                                    }

                                    return $comment . PHP_EOL;
                                })(),
                                'class_attributeBlock' => (function () use ($file) {
                                    $attributeBlock = $this->renderAttributeBlock($file->attributes);
                                    if (null === $attributeBlock) {
                                        return '';
                                    }

                                    return $attributeBlock . PHP_EOL;
                                })(),
                            ])
                    ),
                    'class_line' => $this->renderClassFinal($file->final) . $this->renderClassReadonly($file->readonly) . 'class' . $this->renderClassName($file->objectName) . $this->renderExtends($file->extends) . $this->renderImplementations($file->implementations),
                    'class_content' => [] === $content
                        ? null
                        : implode(
                            PHP_EOL . PHP_EOL,
                            $content
                        ),
                ]
            )
        );
    }

    /**
     * @param ObjectType[] $uses
     *
     * @throws \JsonException
     */
    public function renderInterfaceFile(PhpInterface $file, array $uses): string
    {
        $content = array_filter([
            'class_traits' => $this->traitRenderer->renderTraitsBlock($file->traits),
            'class_constants' => $this->constantRenderer->renderConstantBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->constants)
            ),
            'class_properties' => $this->renderPropertyBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->properties),
                $uses
            ),
            'class_methods' => $this->renderMethodBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->methods),
                $file->objectNamespace,
                $uses
            ),
        ]);

        return $this->renderer->render(
            new Renderable(
                template: self::template(),
                variables: [
                    'php_eol' => PHP_EOL,
                    'class_out' => implode(
                        PHP_EOL,
                        array_filter([
                            'strictTypes' => $this->renderStrictTypes($file->strictTypes),
                            'namespace' => $this->renderNamespace($file->objectNamespace),
                            'useBlock' => $this->usesRenderer->renderUsesBlock($uses, $file->objectType),
                        ])
                    ),
                    'class_doc' => PHP_EOL . implode(
                        '',
                        array_filter([
                                'class_comment' => (function () use ($file) {
                                    $comment = $this->renderComment($file->comment);
                                    if (null === $comment) {
                                        return '';
                                    }

                                    return $comment . PHP_EOL;
                                })(),
                                'class_attributeBlock' => (function () use ($file) {
                                    $attributeBlock = $this->renderAttributeBlock($file->attributes);
                                    if (null === $attributeBlock) {
                                        return '';
                                    }

                                    return $attributeBlock . PHP_EOL;
                                })(),
                            ])
                    ),
                    'class_line' => $this->renderClassFinal($file->final) . 'interface' . $this->renderClassName($file->objectName) . $this->renderExtends($file->extends) . $this->renderImplementations($file->implementations),
                    'class_content' => [] === $content
                        ? null
                        : implode(
                            PHP_EOL . PHP_EOL,
                            $content
                        ),
                ]
            )
        );
    }

    /**
     * @param ObjectType[] $uses
     *
     * @throws \JsonException
     */
    public function renderEnumFile(PhpEnum $file, array $uses): string
    {
        $content = array_filter([
            'class_traits' => $this->traitRenderer->renderTraitsBlock($file->traits),
            'class_constants' => $this->constantRenderer->renderConstantBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->constants)
            ),
            'class_cases' => $this->enumCaseRenderer->renderCaseBlock(
                $file->cases
            ),
            'class_properties' => $this->renderPropertyBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->properties),
                $uses
            ),
            'class_methods' => $this->renderMethodBlock(
                $this->accessModifierSort->sortElementsByAccessModifier($file->methods),
                $file->objectNamespace,
                $uses
            ),
        ]);

        return $this->renderer->render(
            new Renderable(
                template: self::template(),
                variables: [
                    'php_eol' => PHP_EOL,
                    'class_out' => implode(
                        PHP_EOL,
                        array_filter([
                            'strictTypes' => $this->renderStrictTypes($file->strictTypes),
                            'namespace' => $this->renderNamespace($file->objectNamespace),
                            'useBlock' => $this->usesRenderer->renderUsesBlock($uses, $file->objectType),
                        ])
                    ),
                    'class_doc' => PHP_EOL . implode(
                        '',
                        array_filter([
                                'class_comment' => (function () use ($file) {
                                    $comment = $this->renderComment($file->comment);
                                    if (null === $comment) {
                                        return '';
                                    }

                                    return $comment . PHP_EOL;
                                })(),
                                'class_attributeBlock' => (function () use ($file) {
                                    $attributeBlock = $this->renderAttributeBlock($file->attributes);
                                    if (null === $attributeBlock) {
                                        return '';
                                    }

                                    return $attributeBlock . PHP_EOL;
                                })(),
                            ])
                    ),
                    'class_line' => $this->renderClassFinal($file->final) . 'enum' . $this->renderClassName($file->objectName) . $this->renderExtends($file->extends) . $this->renderImplementations($file->implementations) . $this->renderEnumReturnType($file->returnType),
                    'class_content' => [] === $content
                        ? null
                        : implode(
                            PHP_EOL . PHP_EOL,
                            $content
                        ),
                ]
            )
        );
    }

    /**
     * class_content:
     * {{class_traits}}
     * {{class_constants}}
     * {{class_properties}}
     * {{class_methods}}.
     *
     * class_out:
     * {{strictTypes}}
     * {{namespace}}
     * {{useBlock}}
     *
     * class_doc:
     * {{class_comment}}
     * {{class_attributeBlock}}
     */
    private function template(): string
    {
        return <<<'TEMPLATE'
            <?php

            {{class_out}}{{class_doc}}{{class_line}}
            {
            {{class_content}}
            }

            TEMPLATE;
    }

    public function renderStrictTypes(bool $strictTypes): ?string
    {
        return $strictTypes
            ? 'declare(strict_types=1);' . PHP_EOL
            : null;
    }

    public function renderNamespace(ObjectNamespace|null $namespace): ?string
    {
        return null !== $namespace
            ? "namespace $namespace->value;" . PHP_EOL
            : null;
    }

    public function renderExtends(ObjectType|null $extends): ?string
    {
        return null !== $extends
            ? ' extends ' . $this->classNameResolver->resolve($extends)
            : null;
    }

    private function renderEnumReturnType(?TypeInterface $returnType): ?string
    {
        return null === $returnType ? null : ': ' . $returnType->toString();
    }

    /**
     * @param ObjectType[]|null $implementations
     */
    public function renderImplementations(array|null $implementations): ?string
    {
        return null !== $implementations && [] !== $implementations
            ? ' implements ' . implode(
                ', ',
                array_map(
                    function (ObjectType $implementation) {
                        return $this->classNameResolver->resolve($implementation);
                    },
                    $implementations
                )
            )
            : null;
    }

    /**
     * @param Property[]|null $properties
     * @param ObjectType[]    $uses
     *
     * @throws \JsonException
     */
    public function renderPropertyBlock(array|null $properties, array $uses): ?string
    {
        return null !== $properties && [] !== $properties
            ? implode(
                PHP_EOL . PHP_EOL,
                array_map(
                    function (Property $property) use ($uses) {
                        return $this->renderProperty($property, $uses);
                    },
                    $properties
                )
            )
            : null;
    }

    /**
     * @param Method[]|null $methods
     * @param ObjectType[]  $uses
     *
     * @throws \JsonException
     */
    public function renderMethodBlock(array|null $methods, ObjectNamespace|null $namespace, array $uses): ?string
    {
        return null !== $methods && [] !== $methods
            ? implode(
                PHP_EOL . PHP_EOL,
                array_map(
                    function (Method $method) use ($namespace, $uses) {
                        return $this->renderMethod($method, $namespace, $uses);
                    },
                    $methods
                )
            )
            : null;
    }

    /**
     * @param ObjectType[] $uses
     *
     * @throws \JsonException
     */
    public function renderMethod(Method $method, ObjectNamespace|null $namespace, array $uses): ?string
    {
        $renderParameter = function (Parameter $parameter) use ($uses, $namespace) {
            $stack = [
                0 => $parameter->accessModifier?->value,       // modifier
                1 => $parameter->readonly ? 'readonly' : null, // readonly
                2 => null,                                     // type
                3 => '$' . $parameter->name,                   // name
                4 => null,                                     // default
            ];

            if (null !== $parameter->type) {
                if (isset($uses[$parameter->type->toString()])) {
                    $paramTypeValue = $uses[$parameter->type->toString()]->alias ?? $uses[$parameter->type->toString()]->objectName->value;
                    $paramType = $parameter->nullable ? '?' . ($paramTypeValue) : $paramTypeValue;
                } elseif ($parameter->type instanceof NativeType) {
                    $paramType = $parameter->nullable ? '?' . $parameter->type->toString() : $parameter->type->toString();
                } else {
                    $explodeParamNamespace = explode('\\', $parameter->type->toString());
                    $paramClassName = array_pop($explodeParamNamespace);
                    $paramNamespace = implode('\\', $explodeParamNamespace);
                    if ($paramNamespace === $namespace?->value) {
                        $paramType = $parameter->nullable ? '?' . $paramClassName : $paramClassName;
                    } else {
                        $paramType = $parameter->nullable ? '?\\' . $parameter->type->toString() : '\\' . $parameter->type->toString();
                    }
                }
                $stack[2] = $paramType;
            }
            if ($parameter->defaultValue->isset) {
                $stack[4] = '= ' . ($parameter->defaultValue->shieldValue ? $this->shielder->shieldValue($parameter->defaultValue->value) : $parameter->defaultValue->value);
            }

            return implode(' ', array_filter($stack));
        };

        $renderTypePrintValue = function (TypeInterface $type) use ($uses, $namespace) {
            if (isset($uses[$type->toString()])) {
                return $type->isNullable()
                    ? '?' . ($uses[$type->toString()]->alias ?? $uses[$type->toString()]->objectName->value)
                    : $uses[$type->toString()]->alias ?? $uses[$type->toString()]->objectName->value;
            }

            if ($type instanceof NativeType) {
                return $type->isNullable() ? '?' . $type->toString() : $type->toString();
            }
            $explodeParamNamespace = explode('\\', $type->toString());
            $paramClassName = array_pop($explodeParamNamespace);
            $paramNamespace = implode('\\', $explodeParamNamespace);
            if ($paramNamespace === $namespace?->value) {
                return $type->isNullable() ? '?' . $paramClassName : $paramClassName;
            }

            return $type->isNullable() ? '?\\' . $type->toString() : '\\' . $type->toString();
        };

        if (null !== $method->returnType) {
            $returnType = $renderTypePrintValue($method->returnType);
        } else {
            $returnType = '';
        }
        $returnTypeDoublePoint = '' !== $returnType ? ': ' : '';

        $payload = [];
        if (null !== $method->comment && mb_strlen($method->comment->content) > 0) {
            $payload[0] = $this->renderComment($method->comment, 1);
        }
        if (null !== $method->attributes && count($method->attributes) > 0) {
            $payload[1] = $this->renderAttributeBlock($method->attributes, 1);
        }
        $payload[3] = '';
        if (null !== $method->body) {
            if (null === $method->parameters || 0 === count($method->parameters)) {
                $payload[3] .= Dictionary::TAB . $method->accessModifier->value . ' ' . ($method->static ? 'static ' : '') . 'function ' . $method->name->value . '()' . $returnTypeDoublePoint . $returnType . PHP_EOL;
                $payload[3] .= Dictionary::TAB . '{' . PHP_EOL;
                if (mb_strlen($method->body->content) > 0) {
                    $payload[3] .= $this->renderMethodBody($method->body->content);
                }
                $payload[3] .= Dictionary::TAB . '}';
            } elseif (1 === count($method->parameters)) {
                $payload[3] .= Dictionary::TAB . $method->accessModifier->value . ' ' . ($method->static ? 'static ' : '') . 'function ' . $method->name->value . '(' . $renderParameter(current($method->parameters)) . ')' . $returnTypeDoublePoint . $returnType . PHP_EOL;
                $payload[3] .= Dictionary::TAB . '{' . PHP_EOL;
                if (mb_strlen($method->body->content) > 0) {
                    $payload[3] .= $this->renderMethodBody($method->body->content);
                }
                $payload[3] .= Dictionary::TAB . '}';
            } else {
                $payload[3] .= Dictionary::TAB . $method->accessModifier->value . ' ' . ($method->static ? 'static ' : '') . 'function ' . $method->name->value . '(' . PHP_EOL;
                foreach ($method->parameters as $parameter) {
                    $payload[3] .= Dictionary::TAB . Dictionary::TAB . $renderParameter($parameter) . ',' . PHP_EOL;
                }
                $payload[3] .= Dictionary::TAB . ')' . $returnTypeDoublePoint . $returnType . ' {' . PHP_EOL;
                if (mb_strlen($method->body->content) > 0) {
                    $payload[3] .= $this->renderMethodBody($method->body->content);
                }
                $payload[3] .= Dictionary::TAB . '}';
            }
        } elseif (null === $method->parameters || 0 === count($method->parameters)) {
            $payload[3] .= Dictionary::TAB . $method->accessModifier->value . ' ' . ($method->static ? 'static ' : '') . 'function ' . $method->name->value . '()' . $returnTypeDoublePoint . $returnType . ';';
        } elseif (1 === count($method->parameters)) {
            $payload[3] .= Dictionary::TAB . $method->accessModifier->value . ' ' . ($method->static ? 'static ' : '') . 'function ' . $method->name->value . '(' . $renderParameter(current($method->parameters)) . ')' . $returnTypeDoublePoint . $returnType . ';';
        } else {
            $payload[3] .= Dictionary::TAB . $method->accessModifier->value . ' ' . ($method->static ? 'static ' : '') . 'function ' . $method->name->value . '(' . PHP_EOL;
            foreach ($method->parameters as $parameter) {
                $payload[3] .= Dictionary::TAB . Dictionary::TAB . $renderParameter($parameter) . ',' . PHP_EOL;
            }
            $payload[3] .= Dictionary::TAB . ')' . $returnTypeDoublePoint . $returnType . ';';
        }

        return implode(
            PHP_EOL,
            array_filter(
                $payload
            )
        );
    }

    /**
     * @description Этот код был сгенерирован нейросетью:)
     */
    private function renderMethodBody(string $content): string
    {
        $methodBody = '';
        foreach (explode(PHP_EOL, $content) as $line) {
            $methodBody .= ('' === trim($line))
                ? PHP_EOL
                : Dictionary::TAB . Dictionary::TAB . $line . PHP_EOL;
        }

        return $methodBody;
    }

    /**
     * @param ObjectType[] $uses
     *
     * @throws \JsonException
     */
    public function renderProperty(Property $property, array $uses): ?string
    {
        $result = '';

        if (null !== $property->comment && mb_strlen($property->comment->content) > 0) {
            $result .= $this->renderComment($property->comment, 1) . PHP_EOL;
        }
        if (null !== $property->attributes && count($property->attributes) > 0) {
            $result .= $this->renderAttributeBlock($property->attributes, 1) . PHP_EOL;
        }

        if (null !== $property->type) {
            if (isset($uses[$property->type->toString()])) {
                $type = $property->type->isNullable()
                    ? '?' . ($uses[$property->type->toString()]->alias ?? $uses[$property->type->toString()]->objectName->value)
                    : $uses[$property->type->toString()]->alias ?? $uses[$property->type->toString()]->objectName->value;
            } else {
                $type = $property->type->isNullable() ? '?' . $property->type->toString() : $property->type->toString();
            }
            $type .= ' ';
        } else {
            $type = '';
        }
        if ($property->readonly) {
            $readonly = 'readonly ';
        } else {
            $readonly = '';
        }
        if ($property->static) {
            $static = 'static ';
        } else {
            $static = '';
        }
        if ($property->defaultValue->isset) {
            $defaultValue = ' = ' . ($property->defaultValue->shieldValue ? $this->shielder->shieldValue($property->defaultValue->value) : $property->defaultValue->value);
        } else {
            $defaultValue = '';
        }

        return $result .
            Dictionary::TAB . $property->accessModifier->value . ' ' . $static . $readonly . $type . '$' . $property->name->value . $defaultValue . ';';
    }

    public function renderAttribute(Attribute $attribute, int $addTabs = 0): ?string
    {
        $attributeClassName = $this->classNameResolver->resolve($attribute->objectType);
        if (empty($attribute->arguments)) {
            return str_repeat(Dictionary::TAB, $addTabs) . "#[{$attributeClassName}]";
        }
        if (1 === count($attribute->arguments)) {
            $argument = current($attribute->arguments);

            return str_repeat(Dictionary::TAB, $addTabs) . "#[{$attributeClassName}($argument->name: " . ($argument->shieldValue ? $this->shielder->shieldValue($argument->value) : $argument->value) . ')]';
        }

        $result = str_repeat(Dictionary::TAB, $addTabs) . "#[{$attributeClassName}(" . PHP_EOL;
        foreach ($attribute->arguments as $argument) {
            $result .= str_repeat(Dictionary::TAB, $addTabs + 1) . "Helper.php " . ($argument->shieldValue ? $this->shielder->shieldValue($argument->value) : $argument->value) . ',' . PHP_EOL;
        }
        $result .= str_repeat(Dictionary::TAB, $addTabs) . ')]';

        return $result;
    }

    /**
     * @param Attribute[]|null $attributes
     */
    public function renderAttributeBlock(array|null $attributes, int $addTabs = 0): ?string
    {
        return null !== $attributes && [] !== $attributes
            ? implode(
                PHP_EOL,
                array_map(
                    function (Attribute $attribute) use ($addTabs) {
                        return $this->renderAttribute($attribute, $addTabs);
                    },
                    $attributes
                )
            )
            : null;
    }

    public function renderClassName(ObjectName $name): ?string
    {
        return ' ' . $name->value;
    }

    public function renderClassFinal(bool $final): ?string
    {
        return $final ? 'final ' : null;
    }

    public function renderClassReadonly(bool $readonly): ?string
    {
        return $readonly ? 'readonly ' : null;
    }

    public function renderComment(Comment|null $comment, int $addTabs = 0): ?string
    {
        return null !== $comment && mb_strlen($comment->content) > 0
            ? $this->_renderComment($comment->content, $addTabs)
            : null;
    }

    private function _renderComment(?string $content, int $tabs = 0): string
    {
        if (null !== $content) {
            $maxLength = 112;
            if ('UTF-8' === mb_detect_encoding($content)) {
                $maxLength = (int) ($maxLength * 1.4);
            }

            $hasEol = str_contains($content, PHP_EOL);
            if (!$hasEol && strlen($content) <= $maxLength) {
                return str_repeat(Dictionary::TAB, $tabs) . '/** ' . $content . ' */';
            }

            $result = str_repeat(Dictionary::TAB, $tabs) . '/**' . PHP_EOL;
            foreach (explode("\n", wordwrap($content, $maxLength)) as $line) {
                $result .= str_repeat(Dictionary::TAB, $tabs) . ' * ' . rtrim($line) . PHP_EOL;
            }
            $result .= str_repeat(Dictionary::TAB, $tabs) . ' */';

            return $result;
        }

        return '';
    }
}
