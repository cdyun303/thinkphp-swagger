<?php
/**
 * UseAuth
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/15 00:44
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Attributes;

use Cdyun\ThinkphpSwagger\Schemas\AuthSchemas;

/**
 * 指定接口认证方式
 *
 * 用法示例：
 *
 *   // （默认）
 *   自动注入 Bearer Token
 *
 *   // API Key（Header）
 *   #[UseAuth(AuthSchemas::API_KEY)]
 *
 *   // 使用 Basic 认证
 *   #[UseAuth(AuthSchemas::BASIC)]
 *
 *   // 跳过认证
 *   #[NoAutoAuth]
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class UseAuth
{
    public function __construct(
        public readonly string $type = AuthSchemas::BEARER,
    )
    {
    }
}