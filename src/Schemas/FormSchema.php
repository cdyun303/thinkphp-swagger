<?php
/**
 * FormSchema
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 22:45
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Schemas;

/**
 * 表单字段 Schema
 */
final class FormSchema
{
    /**
     * @param array<int, array{name: string, schema?: array<string, mixed>, required?: bool, description?: string}> $fields
     *
     * @return array<int, array{name: string, schema?: array<string, mixed>, required?: bool, description?: string}>
     */
    public static function fields(array $fields): array
    {
        return $fields;
    }
}