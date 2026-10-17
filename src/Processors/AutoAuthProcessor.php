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
use OpenApi\Annotations as OA;

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
        $attr = $this->readAttribute($operation, UseAuth::class);
        return [AuthSchemas::definition($attr?->type, $attr?->field)];
    }
}