<?php
/**
 * UseForm
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 22:24
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Attributes;

/**
 * 表单参数
 *
 * @param array<int, array{name: string, schema?: array<string, mixed>, required?: bool, description?: string}> $fields 字段数组
 * @param string $description 描述信息
 * @param string $contentType 内容类型，默认为application/json
 * @param bool $dual 是否同时生成两种类型，默认为false
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class UseForm
{
    public const JSON = 'application/json';
    public const FORM = 'application/x-www-form-urlencoded';

    public function __construct(
        public readonly array $fields = [],
        public readonly ?string $description = null,
        public readonly string $contentType = self::JSON,
        public readonly bool $dual = false,
    ) {
    }
}