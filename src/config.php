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
            'type' => 1, // 类型：[1:Bearer 认证令牌（默认）, 2:Basic 认证, 3:自定义认证]
            'field' => 'Authorization', // 认证字段名称
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
