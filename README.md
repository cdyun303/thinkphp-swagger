Thinkphp Swagger 扩展
=====

## 概述

本项目基于 `swagger-php` 实现了 4 个自定义 Processor，通过 PHP 8 Attribute 注解自动为接口注入认证参数、分页参数、统一响应体和表单请求体。

## 安装

```
composer require cdyun/thinkphp-swagger
```

## 目录结构

```
src/
├── Attributes/
│   ├── UseAuth.php            # 指定认证方式
│   ├── NoAutoAuth.php         # 跳过认证注入
│   ├── UsePagination.php      # 标注需要分页参数
│   ├── UseResponse.php        # 自定义响应体
│   ├── NoAutoResponse.php     # 跳过响应注入
│   └── UseForm.php            # 表单参数定义
├── Schemas/
│   ├── AuthSchemas.php        # 认证参数定义
│   ├── PaginationParams.php   # 分页参数定义
│   ├── ResponseSchema.php     # 统一响应格式
│   └── FormSchema.php         # 表单 Schema 工具
├── Processors/
│   ├── BaseAutoProcessor.php       # 公共基类
│   ├── AutoAuthProcessor.php       # 认证参数注入
│   ├── AutoPaginationProcessor.php # 分页参数注入
│   ├── AutoResponseProcessor.php   # 响应体自动注入
│   └── AutoFormProcessor.php       # 表单参数注入
├── config.php        # 配置文件
├── Swagger.php       # Swagger核心类
├── SwaggerController.php       # Swagger控制器
└── SwaggerService.php       # Swagger服务注入

```

## - 服务注入
在扩展的composer.json文件中增加如下定义：
```
"extra": {
    "think": {
        "services": [
            "Cdyun\\ThinkphpSwagger\\SwaggerService"
        ]
    }
},
```
实现系统初始化过程中自动注册SwaggerService服务：
```
<?php
namespace Cdyun\ThinkphpSwagger;

use think\Route;
use think\Service;

class SwaggerService extends Service
{
    public function boot()
    {
        // 注册路由
        $this->registerRoutes(function (Route $route) {
            // 获取swagger Json信息，http://xxx.com/swagger/openapi.json
            $route->get('swagger/openapi', '\\Cdyun\\ThinkphpSwagger\\SwaggerController@openapi')->ext('json');
            // 访问swagger页面，http://xxx.com/swagger
            $route->get('swagger', '\\Cdyun\\ThinkphpSwagger\\SwaggerController@index');
        });
    }
}
```

## - （配置文件）config/swagger.php
```PHP
<?php

return [
    // 应用分组
    'groups'=>[
        // 应用名称
        'default'=>[
            // 标题，会替换OA\Info中标题
            'title'=>'通用接口文档',
            // 描述，会替换OA\Info中描述
            'description'=>'让开发变得更简单、更通用、更流行。',
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
```

## - 使用注意
###### 1、每个应用的OA\Info信息，必须且只能存在一个，所以建议写在每个应用控制器继承的 BaseController.php 上；
###### 2、OA\Info信息会被 config/swagger.php 中信息替换掉；
例： 应用V1
```
<?php
namespace app\v1\controller;

use support\base\BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'V1')]
class V1BaseController extends BaseController
{
    ...其他
}
```
例： 应用V2
```
<?php
namespace app\v2\controller;

use support\base\BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(version: '2.0.0', title: 'V2')]
class V2BaseController extends BaseController
{
    ...其他
}
```


## 1. AutoAuthProcessor — 认证参数注入【自动注入】

### 功能

自动为接口注入认证参数（Header），支持 Bearer、ApiKey、Basic 三种认证方式。默认使用 Bearer 认证。

### 属性注解

#### `#[UseAuth(type,field)]` — 指定认证方式

| 参数 | 类型       | 默认值 | 说明 |
|------|----------|-----|------|
| `type` | `int`    | `1` | 认证方式：1:Bearer 认证令牌（默认）, 2:Basic 认证, 3:自定义认证 |
| `field` | `string` | `Authorization` | 认证字段名称 |

支持 **方法级** 和 **类级** 标注。方法级优先于类级。

#### `#[NoAutoAuth]` — 跳过认证注入

无参数。标注后该接口不会注入任何认证参数。

### 认证方式对照

| Type 类型 | Header 名        | 说明                          |
|---------|-----------------|-----------------------------|
| `1`     | `Authorization` | Bearer 令牌（`Bearer <token>`） |
| `2`     | `Authorization` | Basic 认证（`Basic <base64>`）  |
| `3`     | `Authorization` | 自定义认证                       |

### 使用示例

```php
use Cdyun\ThinkphpSwagger\Attributes as CTA;

class TestController
{
    // 默认 Bearer 认证（无需标注，Processor 自动注入）
    public function t1(): void {}
    
    // 使用 Bearer 认证（自定义 Header 名）
    #[CTA\UseAuth(field: 'X-Auth-Token')]
    public function t2(): void {}
    
    // 方法级覆盖类级，使用 Basic 认证（自定义 Header 名）
    #[CTA\UseAuth(type: 2)]
    public function t3(): void {}

    // 方法级覆盖类级，使用自定义认证
    #[CTA\UseAuth(type: 3)]
    public function t4(): void {}

    // 跳过认证（公开接口）
    #[CTA\NoAutoAuth]
    public function t5(): void {}
}
```
---

## 2. AutoPaginationProcessor — 分页参数注入【手动注入】

### 功能

为标注了 `#[UsePagination]` 的接口注入分页查询参数（`page`、`size`）。支持自定义参数名和默认值。

### 属性注解

#### `#[UsePagination(...)]`

| 参数 | 类型 | 默认值 | 说明 |
|------|------|--------|------|
| `pageName` | `?string` | `'page'` | 页码参数名 |
| `sizeName` | `?string` | `'size'` | 每页数量参数名 |
| `pageDefault` | `?int` | `1` | 页码默认值 |
| `sizeDefault` | `?int` | `20` | 每页数量默认值 |

**按需标注**：未标注的接口不会注入分页参数。

### 使用示例

```php
use Cdyun\ThinkphpSwagger\Attributes as CTA;

class TestController
{
    // 默认分页参数：page=1, size=20
    #[CTA\UsePagination]
    #[OA\Get(path: '/users', ...)]
    public function listUsers(): void {}
    
    // 自定义参数名和默认值
    #[CTA\UsePagination(pageName: 'pageNum', sizeName: 'pageSize', sizeDefault: 50)]
    #[OA\Get(path: '/products', ...)]
    public function listProducts(): void {}
}
```

### 注入结果

| 标注 | 注入参数 |
|------|---------|
| `#[UsePagination]` | `page`(默认1), `size`(默认20) |
| `#[UsePagination(pageName: 'pageNum', sizeName: 'pageSize', sizeDefault: 50)]` | `pageNum`(默认1), `pageSize`(默认50) |
| 无标注 | 无 |

---

## 3. AutoResponseProcessor — 响应体【自动注入】

### 功能

自动为没有手动定义 responses 的接口注入统一响应格式 `{code, message, data}`。

### 属性注解

#### `#[UseResponse(code, description)]` — 自定义响应

| 参数 | 类型       | 默认值     | 说明       |
|------|----------|---------|----------|
| `code` | `int`    | `200`   | HTTP 状态码 |
| `description` | `string` | `'成功'`  | 响应描述     |
| `page` | `bool`   | `false` | 是否分页     |

#### `#[NoAutoResponse]` — 跳过响应注入

无参数。

### 统一响应格式

```json
// 默认响应
{
    "code": 0,
    "message": "success",
    "data": {}
}
```
```json
// 分页响应
{
    "code": 0,
    "message": "success",
    "data": {
      "items": [],
      "total": 0,
    }
}
```

### 覆盖规则

- 已有手动 `responses` 的接口 **不会被覆盖**
- 无 `responses` 的接口自动注入统一响应
- `#[NoAutoResponse]` 标注的接口跳过注入

### 使用示例

```php
use Cdyun\ThinkphpSwagger\Attributes as CTA;

class TestController
{
    // 自动注入默认 200 响应
    #[OA\Post(path: '/users/login', ...)]
    public function login(): void {}
    
    // 自定义响应码和描述
    #[CTA\UseResponse(code: 200, description: '商品列表')]
    #[OA\Get(path: '/products', ...)]
    public function listProducts(): void {}
    
    // 跳过自动注入（已有手动 responses 时无需标注，自动跳过）
    #[CTA\NoAutoResponse]
    #[OA\Get(path: '/users/{id}', ..., responses: [
        new OA\Response(response: 200, description: '成功', ...),
        new OA\Response(response: 404, description: '不存在'),
    ])]
    public function getUser(): void {}
}
```

### 注入结果

| 场景 | 响应 |
|------|------|
| 无手动 responses，无标注 | 自动注入 `200` + 统一格式 |
| 无手动 responses，`#[UseResponse(201, '创建成功')]` | 自动注入 `201` + 统一格式 |
| 有手动 responses | 保持手动定义，不覆盖 |
| `#[NoAutoResponse]` | 不注入 |

---

## 4. AutoFormProcessor — 表单参数注入【手动注入】

### 功能

为标注了 `#[UseForm]` 的接口自动构建 requestBody，支持 `application/json` 和 `application/x-www-form-urlencoded` 两种类型，可手动切换或双类型并存。

### 属性注解

#### `#[UseForm(...)]`

| 参数 | 类型 | 默认值 | 说明 |
|------|------|--------|------|
| `fields` | `array` | `[]` | 字段定义数组 |
| `description` | `?string` | `null`（默认`'表单数据'`） | requestBody 描述 |
| `contentType` | `string` | `UseForm::JSON` | 内容类型 |
| `dual` | `bool` | `false` | 是否同时生成两种类型 |

#### 常量

| 常量 | 值 |
|------|-----|
| `UseForm::JSON` | `application/json` |
| `UseForm::FORM` | `application/x-www-form-urlencoded` |

#### `fields` 字段格式

每个字段为关联数组：

```php
[
    'name' => '字段名',           // 必填
    'schema' => ['type' => 'string', ...],  // 选填，默认 type=string
    'required' => true,           // 选填，默认 false
    'description' => '字段描述',   // 选填
]
```

### 覆盖规则

- 已有手动 `requestBody` 的接口 **不会被覆盖**
- 无 `requestBody` 且标注了 `#[UseForm]` 的接口自动注入

### 使用示例

```php
use Cdyun\ThinkphpSwagger\Attributes\UseForm;

class TestController
{
    // 示例1：默认 JSON 类型
    #[UseForm(
        fields: [
            ['name' => 'username', 'schema' => ['type' => 'string'], 'required' => true, 'description' => '用户名'],
            ['name' => 'password', 'schema' => ['type' => 'string', 'format' => 'password'], 'required' => true, 'description' => '密码'],
        ],
        description: '登录表单',
    )]
    #[OA\Post(path: '/users/login', ...)]
    public function login(): void {}
    
    // 示例2：表单类型（x-www-form-urlencoded）
    #[UseForm(
        fields: [
            ['name' => 'username', 'schema' => ['type' => 'string'], 'required' => true, 'description' => '用户名'],
            ['name' => 'email', 'schema' => ['type' => 'string', 'format' => 'email'], 'required' => true, 'description' => '邮箱'],
        ],
        description: '注册表单',
        contentType: UseForm::FORM,
    )]
    #[OA\Post(path: '/users/register', ...)]
    public function register(): void {}
    
    // 示例3：双类型（前端 SwaggerUI 可切换）
    #[UseForm(
        fields: [
            ['name' => 'username', 'schema' => ['type' => 'string'], 'required' => true],
            ['name' => 'password', 'schema' => ['type' => 'string', 'format' => 'password'], 'required' => true],
            ['name' => 'remember', 'schema' => ['type' => 'boolean'], 'required' => false],
        ],
        description: '登录表单',
        dual: true,
    )]
    #[OA\Post(path: '/users/login', ...)]
    public function login(): void {}
}
```

### 注入结果

| 标注 | requestBody content |
|------|---------------------|
| 默认 | `application/json` |
| `contentType: UseForm::FORM` | `application/x-www-form-urlencoded` |
| `dual: true` | 同时包含两种类型，SwaggerUI 显示下拉切换器 |

---

## 组合使用

多个注解可组合使用，Processor 按注册顺序依次执行：

```php
use Cdyun\ThinkphpSwagger\Attributes as CTA;

#[CTA\UseAuth(AuthSchemas::API_KEY)]  // 类级：所有接口使用 ApiKey 认证
class ProductController
{
    #[CTA\UsePagination(pageName: 'pageNum', sizeName: 'pageSize', sizeDefault: 50)]
    #[CTA\UseResponse(code: 200, description: '商品列表')]
    #[OA\Get(path: '/products', ...)]
    public function listProducts(): void {}
    // 注入：X-Api-Key + pageNum + pageSize + 统一响应

    #[CTA\UseForm(
        fields: [
            ['name' => 'title', 'schema' => ['type' => 'string'], 'required' => true],
            ['name' => 'price', 'schema' => ['type' => 'number'], 'required' => true],
        ],
        description: '商品信息',
    )]
    #[OA\Post(path: '/products', ...)]
    public function createProduct(): void {}
    // 注入：X-Api-Key + JSON requestBody + 统一响应
}
```

## 执行顺序

Processor 在 swagger-php pipeline 末尾依次执行：

```
AutoAuthProcessor → AutoPaginationProcessor → AutoResponseProcessor → AutoFormProcessor
```

- **认证**：自动注入 Header 参数
- **分页**：注入 Query 参数
- **响应**：自动注入 responses（无手动 responses 时）
- **表单**：注入 requestBody（无手动 requestBody 时）