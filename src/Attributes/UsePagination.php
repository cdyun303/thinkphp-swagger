<?php
/**
 * UsePagination
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 22:35
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Attributes;

/**
 * 分页参数
 * @param string|null $pageName 页码参数名
 * @param int|null $pageDefault 页码参数名默认值
 * @param string|null $sizeName 每页数量参数名称
 * @param int|null $sizeDefault 每页数量参数默认值
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class UsePagination
{
    public function __construct(
        public readonly ?string $pageName = null,
        public readonly ?string $sizeName = null,
        public readonly ?int $pageDefault = null,
        public readonly ?int $sizeDefault = null,
    ) {
    }
}