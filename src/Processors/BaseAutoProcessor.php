<?php
/**
 * BaseAutoProcessor
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/15 00:34
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use ReflectionMethod;

/**
 * 公共基类，提供：
 *  - 从 Operation 的方法/类上读取自定义 attribute（UseAuth / NoAutoAuth等）
 *  - 向 components.securitySchemes 注册 SecurityScheme
 */
abstract class BaseAutoProcessor
{
    /**
     * @param OA\Operation $operation
     *
     * @return array<int, array{name: string, in: string, description?: string, required?: bool, schema: array<string, mixed>}>
     */
    abstract protected function resolveParameters(OA\Operation $operation): array;

    protected function shouldSkip(OA\Operation $operation): bool
    {
        return false;
    }

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        foreach ($operations as $operation) {
            if ($this->shouldSkip($operation)) {
                continue;
            }
            $definitions = $this->resolveParameters($operation);
            if ($definitions === []) {
                continue;
            }
            $parameters = [];
            foreach ($definitions as $definition) {
                $parameters[] = $this->buildParameter($definition, $operation);
            }
            $analysis->mergeAnnotations($operation, $parameters);
        }
    }

    /**
     * @param array{name: string, in: string, description?: string, required?: bool, schema: array<string, mixed>} $definition
     */
    protected function buildParameter(array $definition, OA\Operation $operation): OA\Parameter
    {
        $reflector = $operation->_context->reflector ?? null;
        $context = new Context(['generated' => true, 'reflector' => $reflector], $operation->_context);

        $schema = new OA\Schema($definition['schema'] ?? []);

        $props = [
            'name' => $definition['name'],
            'in' => $definition['in'],
            'schema' => $schema,
            '_context' => $context,
        ];
        if (isset($definition['description'])) {
            $props['description'] = $definition['description'];
        }
        if (isset($definition['required'])) {
            $props['required'] = $definition['required'];
        }

        return new OA\Parameter($props);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $attribute
     *
     * @return T|null
     */
    protected function readAttribute(OA\Operation $operation, string $attribute): ?object
    {
        $reflector = $operation->_context->reflector ?? null;
        if (!$reflector instanceof ReflectionMethod) {
            return null;
        }
        $attrs = $reflector->getAttributes($attribute);
        if ($attrs !== []) {
            return $attrs[0]->newInstance();
        }
        $class = $reflector->getDeclaringClass();
        $attrs = $class->getAttributes($attribute);
        if ($attrs !== []) {
            return $attrs[0]->newInstance();
        }

        return null;
    }
}