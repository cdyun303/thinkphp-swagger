<?php
/**
 * UseResponse
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 22:39
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Attributes;

/**
 * 响应体参数
 * */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class UseResponse
{
    public function __construct(
        public readonly ?int    $httpCode = null, // 状态码
        public readonly ?string $description = null, // 描述
        public readonly ?bool   $page = null, // 是否分页
    )
    {
    }
}