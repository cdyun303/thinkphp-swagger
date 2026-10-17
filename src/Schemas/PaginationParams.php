<?php
/**
 * PaginationParams
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 22:47
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Schemas;

use Cdyun\ThinkphpSwagger\Swagger;

/**
 * 分页参数 Schema
 */
final class PaginationParams
{
    private const DEFAULT_PAGE_NAME = 'page';
    private const DEFAULT_SIZE_NAME = 'size';
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_SIZE = 20;

    /**
     * @return array<int, array{name: string, in: string, description: string, required: bool, schema: array<string, mixed>}>
     */
    public static function definitions(): array
    {
        return self::build();
    }

    /**
     * @return array<int, array{name: string, in: string, description: string, required: bool, schema: array<string, mixed>}>
     */
    public static function build(
        ?string $pageName = null,
        ?string $sizeName = null,
        ?int $pageDefault = null,
        ?int $sizeDefault = null,
    ): array {
        $swagger = new Swagger();
        $pageName ??= $swagger->configure('processor.pagination.page_field', self::DEFAULT_PAGE_NAME);
        $sizeName ??= $swagger->configure('processor.pagination.size_field', self::DEFAULT_SIZE_NAME);
        $pageDefault ??= $swagger->configure('processor.pagination.page_default', self::DEFAULT_PAGE);
        $sizeDefault ??= $swagger->configure('processor.pagination.size_default', self::DEFAULT_SIZE);

        return [
            [
                'name' => $pageName,
                'in' => 'query',
                'description' => '页码',
                'required' => false,
                'schema' => ['type' => 'integer', 'default' => $pageDefault, 'example' => $pageDefault],
            ],
            [
                'name' => $sizeName,
                'in' => 'query',
                'description' => '每页数量',
                'required' => false,
                'schema' => ['type' => 'integer', 'default' => $sizeDefault, 'example' => $sizeDefault],
            ],
        ];
    }
}