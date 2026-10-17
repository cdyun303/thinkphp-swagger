<?php
/**
 * AuthSchemas
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/15 00:51
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Schemas;

use Cdyun\ThinkphpSwagger\Swagger;

/**
 * Authorization认证 Schema 定义：集中管理所有可用认证方案。
 */
final class AuthSchemas
{
    public const TYPE = 1;
    public const FIELD = 'Authorization';

    private static function getDefaultType(?int $type = null)
    {
        $swagger = new Swagger();
        return $type ?? $swagger->configure('processor.auth.type', self::TYPE);
    }

    private static function getDefaultField(?string $field = null)
    {
        $swagger = new Swagger();
        return $field ?? $swagger->configure('processor.auth.field', self::FIELD);
    }

    /**
     * @return array{name: string, in: string, description: string, required: bool, schema: array<string, mixed>}
     */
    public static function definition(?int $type = null, ?string $field = null): array
    {
        $type = self::getDefaultType($type);
        $field = self::getDefaultField($field);
        if ($type === 1) {
            return [
                'name' => $field,
                'in' => 'header',
                'description' => 'Bearer 认证令牌（格式：Bearer <token>）',
                'required' => true,
                'schema' => ['type' => 'string', 'example' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9'],
            ];
        }
        if ($type === 2) {
            return [
                'name' => $field,
                'in' => 'header',
                'description' => 'Basic 认证（格式：Basic <base64(user:pass)>）',
                'required' => true,
                'schema' => ['type' => 'string', 'example' => 'Basic dXNlcjpwYXNz'],
            ];
        }
        if ($type === 3) {
            return [
                'name' => $field,
                'in' => 'header',
                'description' => 'API Key 认证',
                'required' => true,
                'schema' => ['type' => 'string', 'example' => 'a1b2c3d4e5f6'],
            ];
        }
        throw new \InvalidArgumentException(sprintf('未知的认证方式: %s', $type));
    }
}