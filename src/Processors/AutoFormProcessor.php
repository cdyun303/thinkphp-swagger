<?php
/**
 * AutoFormProcessor
 * @author cdyun(121625706@qq.com)
 * @date 2026/10/16 23:03
 */

declare (strict_types=1);

namespace Cdyun\ThinkphpSwagger\Processors;

use Cdyun\ThinkphpSwagger\Attributes\UseForm;
use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Undefined;

/**
 * 注入表单参数
 */
final class AutoFormProcessor extends BaseAutoProcessor
{
    public function __invoke(Analysis $analysis): void
    {
        /** @var OA\Operation[] $operations */
        $operations = $analysis->getAnnotationsOfType(OA\Operation::class);
        foreach ($operations as $operation) {
            $useForm = $this->readAttribute($operation, UseForm::class);
            if ($useForm === null) {
                continue;
            }
            if (!Undefined::isDefault($operation->requestBody)) {
                continue;
            }
            $requestBody = $this->buildRequestBody($useForm, $operation);
            $analysis->mergeAnnotations($operation, [$requestBody]);
        }
    }

    protected function resolveParameters(OA\Operation $operation): array
    {
        return [];
    }

    private function buildRequestBody(UseForm $useForm, OA\Operation $operation): OA\RequestBody
    {
        $context = new Context(['generated' => true], $operation->_context);

        $contentTypes = $useForm->dual
            ? [UseForm::JSON, UseForm::FORM]
            : [$useForm->contentType];

        $content = [];
        foreach ($contentTypes as $ct) {
            $schema = $this->buildSchema($useForm->fields, $context);
            $content[$ct] = new OA\MediaType([
                'schema' => $schema,
                'mediaType' => $ct,
                '_context' => new Context(['generated' => true], $context),
            ]);
        }

        return new OA\RequestBody([
            'description' => $useForm->description ?? '表单数据',
            'required' => true,
            'content' => $content,
            '_context' => $context,
        ]);
    }

    /**
     * @param array<int, array{name: string, schema?: array<string, mixed>, required?: bool, description?: string}> $fields
     */
    private function buildSchema(array $fields, Context $context): OA\Schema
    {
        $properties = [];
        $required = [];
        foreach ($fields as $field) {
            $propProps = ['property' => $field['name']];
            if (isset($field['description'])) {
                $propProps['description'] = $field['description'];
            }
            foreach ($field['schema'] ?? ['type' => 'string'] as $k => $v) {
                $propProps[$k] = $v;
            }
            $properties[] = new OA\Property($propProps);
            if ($field['required'] ?? false) {
                $required[] = $field['name'];
            }
        }

        $schemaProps = [
            'type' => 'object',
            'properties' => $properties,
            '_context' => new Context(['generated' => true], $context),
        ];
        if ($required !== []) {
            $schemaProps['required'] = $required;
        }

        return new OA\Schema($schemaProps);
    }
}