<?php
/**
 * AuthSchemas
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/15 00:51
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Schemas;

use Cdyun\ThinkphpSwagger\Swagger;


final class AuthSchemas
{
    public const BEARER = 'bearer';
    public const API_KEY = 'apiKey';
    public const BASIC = 'basic';
    public const DEFAULT = self::BEARER;

    public const SCHEME_KEY_BEARER = 'BearerAuth';
    public const SCHEME_KEY_API_KEY = 'ApiKeyAuth';
    public const SCHEME_KEY_BASIC = 'BasicAuth';

    public const API_KEY_FIELD = 'X-Api-Key';

    /**
     * @return array<string, array{key: string, type: string, description: string, scheme?: string, bearerFormat?: string, in?: string, name?: string}>
     */
    public static function securitySchemes(): array
    {
        $swagger = new Swagger();
        return [
            self::BEARER => [
                'key' => self::SCHEME_KEY_BEARER,
                'type' => 'http',
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT',
                'description' => 'Bearer 认证（JWT 令牌）',
            ],
            self::API_KEY => [
                'key' => self::SCHEME_KEY_API_KEY,
                'type' => 'apiKey',
                'in' => 'header',
                'name' => $swagger->configure('processor.auth.apiKey', self::API_KEY_FIELD),
                'description' => 'API Key 认证',
            ],
            self::BASIC => [
                'key' => self::SCHEME_KEY_BASIC,
                'type' => 'http',
                'scheme' => 'basic',
                'description' => 'Basic 认证（Base64 编码的 user:pass）',
            ],
        ];
    }

    public static function has(string $type): bool
    {
        return array_key_exists($type, self::securitySchemes());
    }

    /**
     * @return array{key: string, type: string, description: string, scheme?: string, bearerFormat?: string, in?: string, name?: string}
     */
    public static function securityScheme(?string $type = null): array
    {
        $type ??= self::DEFAULT;
        $schemes = self::securitySchemes();
        if (!isset($schemes[$type])) {
            throw new \InvalidArgumentException(sprintf('未知的认证方式: %s', $type));
        }

        return $schemes[$type];
    }

    public static function schemeKey(string $type): string
    {
        return self::securityScheme($type)['key'];
    }
}