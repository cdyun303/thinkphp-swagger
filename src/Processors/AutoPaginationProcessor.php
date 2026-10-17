<?php
/**
 * AutoPaginationProcessor
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 23:09
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Processors;

use Cdyun\ThinkphpSwagger\Attributes\UsePagination;
use Cdyun\ThinkphpSwagger\Schemas\PaginationParams;
use OpenApi\Annotations as OA;

/**
 * 注入表单参数
 */
final class AutoPaginationProcessor extends BaseAutoProcessor
{
    protected function shouldSkip(OA\Operation $operation): bool
    {
        return $this->readAttribute($operation, UsePagination::class) === null;
    }

    protected function resolveParameters(OA\Operation $operation): array
    {
        $attr = $this->readAttribute($operation, UsePagination::class);

        return PaginationParams::build(
            $attr->pageName,
            $attr->sizeName,
            $attr->pageDefault,
            $attr->sizeDefault,
        );
    }
}