<?php
// +----------------------------------------------------------------------
// | Swagger配置文件
// +----------------------------------------------------------------------

return [
    // 文档分组配置
    'groups' => [
        'default' => [
            'title' => '通用接口文档',
            'description' => '让开发变得更简单、更通用、更流行。',
        ],
    ],
    // 自定义 Processor 配置
    'processor' => [
        // 响应
        'response' => [
            //  返回码
            'code' => [
                'success' => 0, //  成功返回码
                'error' => -1, //  失败返回码
            ],
        ],
        // 认证
        'auth' => [
            'type' => 'bearer', // 类型：[bearer（默认值）, basic, apiKey]
            'apiKey' => 'X-Api-Key', // apiKey认证字段名称
        ],
        // 分页
        'pagination' => [
            'page_field' => 'page', // 页码字段名称
            'page_default' => 1, // 页码默认值
            'size_field' => 'size', // 每页数量字段名称
            'size_default' => 20, // 每页数量默认值
        ]
    ]
];
