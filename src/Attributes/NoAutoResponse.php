<?php
/**
 * NoAutoResponse
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 19:12
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Attributes;

/**
 * 标记接口不需要自动注入响应体参数
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class NoAutoResponse
{
}