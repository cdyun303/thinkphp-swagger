<?php
/**
 * AutoAuthProcessor
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/15 00:42
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Processors;

use Cdyun\ThinkphpSwagger\Attributes\NoAutoAuth;
use Cdyun\ThinkphpSwagger\Attributes\UseAuth;
use Cdyun\ThinkphpSwagger\Schemas\AuthSchemas;
use Cdyun\ThinkphpSwagger\Swagger;
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;

/**
 * 自动注入认证参数。
 *
 * 根据 UseAuth（指定认证方式）与 NoAutoAuth（跳过认证）标记，
 * 从 AuthSchemas 读取方案定义，注册 SecurityScheme 并注入 security 引用。
 *
 * 优先级：方法级标记 > 类级标记；未指定时使用默认方案 defaultScheme。
 */

final class AutoAuthProcessor extends BaseAutoProcessor
{
    protected function shouldSkip(OA\Operation $operation): bool
    {
        return $this->readAttribute($operation, NoAutoAuth::class) !== null;
    }

    protected function resolveParameters(OA\Operation $operation): array
    {
        return [];
    }

    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        if ($operations === []) {
            return;
        }

        $usedTypes = [];
        $pending = [];
        $swagger = new Swagger();
        foreach ($operations as $operation) {
            if ($this->shouldSkip($operation)) {
                continue;
            }
            $useAuth = $this->readAttribute($operation, UseAuth::class);
            $type = $useAuth?->type ?? $swagger->configure('processor.auth.type', AuthSchemas::DEFAULT);
            if (!AuthSchemas::has($type)) {
                continue;
            }
            $pending[] = [$operation, $type];
            $usedTypes[$type] = true;
        }

        if ($pending === []) {
            return;
        }

        $this->registerSecuritySchemes($analysis, array_keys($usedTypes));

        foreach ($pending as [$operation, $type]) {
            $operation->security = [[AuthSchemas::schemeKey($type) => []]];
        }
    }

    /**
     * @param list<string> $types
     */
    private function registerSecuritySchemes(Analysis $analysis, array $types): void
    {
        $openapi = $analysis->openapi;
        if ($openapi === null) {
            return;
        }

        $components = $this->ensureComponents($analysis, $openapi);
        $existingKeys = $this->existingSchemeKeys($components);

        foreach ($types as $type) {
            $def = AuthSchemas::securityScheme($type);
            if (isset($existingKeys[$def['key']])) {
                continue;
            }
            $scheme = $this->buildSecurityScheme($def, $components);
            $analysis->mergeAnnotations($components, [$scheme]);
            $existingKeys[$def['key']] = true;
        }
    }

    private function ensureComponents(Analysis $analysis, OA\OpenApi $openapi): OA\Components
    {
        $components = $openapi->components;
        if (!$components instanceof OA\Components) {
            $context = new Context(['generated' => true], $openapi->_context);
            $components = new OA\Components(['_context' => $context]);
            $openapi->components = $components;
            $analysis->addAnnotation($components, $context);
        }

        return $components;
    }

    /**
     * @return array<string, bool>
     */
    private function existingSchemeKeys(OA\Components $components): array
    {
        $keys = [];
        if (is_array($components->securitySchemes)) {
            foreach ($components->securitySchemes as $scheme) {
                if ($scheme instanceof OA\SecurityScheme) {
                    $keys[$scheme->securityScheme] = true;
                }
            }
        }

        return $keys;
    }

    /**
     * @param array{key: string, type: string, description: string, scheme?: string, bearerFormat?: string, in?: string, name?: string} $def
     */
    private function buildSecurityScheme(array $def, OA\Components $components): OA\SecurityScheme
    {
        $context = new Context(['generated' => true], $components->_context);
        $props = [
            'securityScheme' => $def['key'],
            'type' => $def['type'],
            'description' => $def['description'],
            '_context' => $context,
        ];
        foreach (['scheme', 'bearerFormat', 'in', 'name'] as $field) {
            if (isset($def[$field])) {
                $props[$field] = $def[$field];
            }
        }

        return new OA\SecurityScheme($props);
    }
}