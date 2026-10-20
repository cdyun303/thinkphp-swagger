<?php
/**
 * ResponseSchema
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 22:49
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Schemas;

use Cdyun\ThinkphpSwagger\Swagger;
use OpenApi\Attributes as OA;
use OpenApi\Undefined;

/**
 * 响应 Schema
 */
final class ResponseSchema
{
    public const SUCCESS_CODE = 0;
    public const ERROR_CODE = -1;

    /**
     * 统一响应包装格式 Schema：{code, message, data}.
     */
    public static function success(bool $page = false, mixed $data = null): OA\Schema
    {
        $swagger = new Swagger();
        $code ??= $swagger->configure('processor.response.code.success', self::SUCCESS_CODE);
        $properties = [
            new OA\Property(property: 'code', type: 'integer', example: $code, description: '业务状态码'),
            new OA\Property(property: 'message', type: 'string', example: '操作成功'),
        ];
        if ($page) {
            $properties[] = new OA\Property(
                property: 'data',
                type: 'object',
                properties: [
                    new OA\Property(property: 'items', type: 'array', items: new OA\Items(type: 'object')),
                    new OA\Property(property: 'total', type: 'integer', example: 0),
                ],
            );
        } else {
            if ($data !== null && is_array($data) && $data !== []) {
                $properties[] = new OA\Property(
                    property: 'data',
                    type: 'object',
                    properties: self::buildProperty($data, []),
                );
            } else {
                $properties[] = new OA\Property(property: 'data', type: 'object');
            }
        }
        return new OA\Schema(
            type: 'object',
            properties: $properties,
        );
    }

    /**
     * 错误响应包装格式 Schema.
     */
    public static function error(): OA\Schema
    {
        $swagger = new Swagger();
        $code ??= $swagger->configure('processor.response.code.error', self::ERROR_CODE);
        return new OA\Schema(
            type: 'object',
            properties: [
                new OA\Property(property: 'code', type: 'integer', example: $code, description: '业务状态码'),
                new OA\Property(property: 'message', type: 'string', example: '操作失败'),
            ],
        );
    }


    /**
     * 构建data数据 Property.
     */
    private static function buildProperty(array $data, array $schema)
    {
        foreach ($data as $key => $vo) {
            if (!$vo['property'] || !$vo['type']) {
                throw new \RuntimeException('缺少属性或类型');
            }

            $example = $vo['example'] ?? Undefined::UNDEFINED;
            $description = $vo['description'] ?? Undefined::UNDEFINED;
            $properties = $vo['properties'] ?? null;

            if ($vo['type'] === 'array') {
                $schema[] = new OA\Property(
                    property: $vo['property'],
                    type: $vo['type'],
                    example: $example,
                    description: $description,
                    items: new OA\Items(type: 'object'),
                );
            } else if ($properties && is_array($properties) && $properties !== []) {
                $schema[] = new OA\Property(
                    property: $vo['property'],
                    type: $vo['type'],
                    example: $example,
                    description: $description,
                    properties: self::buildProperty($properties, []),
                );
            } else {
                $schema[] = new OA\Property(
                    property: $vo['property'],
                    type: $vo['type'],
                    example: $example,
                    description: $description,
                );
            }
        }

        return $schema;
    }
}