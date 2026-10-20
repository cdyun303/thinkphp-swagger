<?php
/**
 * AutoResponseProcessor
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 23:12
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Processors;

use Cdyun\ThinkphpSwagger\Attributes\NoAutoResponse;
use Cdyun\ThinkphpSwagger\Attributes\UseResponse;
use Cdyun\ThinkphpSwagger\Schemas\ResponseSchema;
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Undefined;

/**
 * 自动注入响应体参数
 */
final class AutoResponseProcessor extends BaseAutoProcessor
{
    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        foreach ($operations as $operation) {
            if ($this->readAttribute($operation, NoAutoResponse::class) !== null) {
                continue;
            }
            if (!Undefined::isDefault($operation->responses)) {
                continue;
            }
            $useResponse = $this->readAttribute($operation, UseResponse::class);
            $successResponse = $this->buildSuccessResponse($useResponse, $operation);
            $errorResponse = $this->buildErrorResponse($useResponse, $operation);
            $analysis->mergeAnnotations($operation, [$successResponse, $errorResponse]);
        }
    }

    protected function resolveParameters(OA\Operation $operation): array
    {
        return [];
    }

    /**
     * 构建成功的响应体
     */
    private function buildSuccessResponse(?UseResponse $useResponse, OA\Operation $operation): OA\Response
    {
        $httpCode = $useResponse?->httpCode ?? 200;
        $description = $useResponse?->description ?? '业务状态：成功';
        $page = $useResponse?->page ?? false;
        $data = $useResponse?->data ?? null;
        $context = new Context(['generated' => true], $operation->_context);

        $schema = ResponseSchema::success($page, $data);
        $mediaType = new OA\MediaType([
            'schema' => $schema,
            'mediaType' => 'application/json',
            '_context' => new Context(['generated' => true], $context),
        ]);

        return new OA\Response([
            'response' => (string)$httpCode,
            'description' => $description,
            'content' => ['application/json' => $mediaType],
            '_context' => $context,
        ]);
    }

    /**
     * 构建失败的响应体
     */
    private function buildErrorResponse(?UseResponse $useResponse, OA\Operation $operation): OA\Response
    {
        $httpCode = $useResponse?->httpCode ?? 204;
        $description = $useResponse?->description ?? '业务状态：失败';
        $page = $useResponse?->page ?? false;
        $context = new Context(['generated' => true], $operation->_context);

        $schema = ResponseSchema::error();
        $mediaType = new OA\MediaType([
            'schema' => $schema,
            'mediaType' => 'application/json',
            '_context' => new Context(['generated' => true], $context),
        ]);

        return new OA\Response([
            'response' => (string)$httpCode,
            'description' => $description,
            'content' => ['application/json' => $mediaType],
            '_context' => $context,
        ]);

    }

}