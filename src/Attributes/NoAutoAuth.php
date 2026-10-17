<?php
/**
 * NoAutoAuth
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/15 00:49
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Attributes;

/**
 * 标记接口不需要自动注入认证参数
 *
 * 适用于：
 *   - 登录/注册等公开接口
 *   - 回调接口（第三方调用，有自己的签名验证）
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class NoAutoAuth
{
}